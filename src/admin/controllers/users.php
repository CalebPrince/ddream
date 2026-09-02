<?php
declare(strict_types=1);

function index(): void
{
    require_login(); require_can('users.manage');
    admin_view('users-index',['title'=>'Users','rows'=>db_all('SELECT id,name,email,role,status,last_login_at,created_at FROM users ORDER BY status="active" DESC,name')]);
}
function create(): void { require_login(); require_can('users.manage'); admin_view('users-form',['title'=>'Add user','account'=>null]); }
function edit(string $id): void
{
    require_login(); require_can('users.manage'); $account=db_one('SELECT * FROM users WHERE id=?',[(int)$id]);
    if(!$account){http_response_code(404);admin_view('error',['code'=>404,'title'=>'User not found','message'=>'That staff account does not exist.']);return;}
    admin_view('users-form',['title'=>'Edit user','account'=>$account]);
}
function store(): never
{
    require_login(); require_can('users.manage');
    $name=(string)input('name',''); $email=mb_strtolower((string)input('email','')); $password=(string)($_POST['password']??''); $role=(string)input('role','admin');
    if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<12||!isset(ROLES[$role])){remember_old($_POST);flash('error','Enter a name, valid email, role, and password of at least 12 characters.');redirect(admin_url('users/new'));}
    if(db_one('SELECT id FROM users WHERE email=?',[$email])){remember_old($_POST);flash('error','That email is already in use.');redirect(admin_url('users/new'));}
    $id=db_insert('users',['name'=>$name,'email'=>$email,'password_hash'=>password_hash($password,PASSWORD_DEFAULT),'role'=>$role,'status'=>'active','created_at'=>now(),'updated_at'=>now()]);
    forget_old();log_activity('created','user',(string)$id,'Created '.$name);flash('success','Staff account created.');redirect(admin_url('users'));
}
function update(string $id): never
{
    require_login(); require_can('users.manage'); $account=db_one('SELECT * FROM users WHERE id=?',[(int)$id]);
    if(!$account){flash('error','User not found.');redirect(admin_url('users'));}
    $name=(string)input('name','');$email=mb_strtolower((string)input('email',''));$role=(string)input('role','admin');$status=(string)input('status','active');$password=(string)($_POST['password']??'');
    if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||!isset(ROLES[$role])||!in_array($status,['active','suspended'],true)||($password!==''&&strlen($password)<12)){flash('error','Check the account details. New passwords must be at least 12 characters.');redirect(admin_url('users/'.$id));}
    if(db_one('SELECT id FROM users WHERE email=? AND id<>?',[$email,(int)$id])){flash('error','That email is already in use.');redirect(admin_url('users/'.$id));}
    if((int)$id===(int)current_user()['id']&&$status!=='active'){flash('error','You cannot suspend your own account.');redirect(admin_url('users/'.$id));}
    if($account['role']==='superadmin'&&($role!=='superadmin'||$status!=='active')&&(int)db_value("SELECT COUNT(*) FROM users WHERE role='superadmin' AND status='active'")<=1){flash('error','The final active Superadmin cannot be removed.');redirect(admin_url('users/'.$id));}
    $data=['name'=>$name,'email'=>$email,'role'=>$role,'status'=>$status,'updated_at'=>now()]; if($password!=='')$data['password_hash']=password_hash($password,PASSWORD_DEFAULT);
    db_update('users',(int)$id,$data);log_activity('updated','user',$id,'Updated '.$name);flash('success','Account updated.');redirect(admin_url('users/'.$id));
}
