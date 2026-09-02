<?php
declare(strict_types=1);

function index(): void
{
    require_login();
    require_can('inbox.view');
    $status = (string) input('status', 'open');
    $type = (string) input('type', '');
    $where = [];
    $params = [];
    if ($status === 'open') $where[] = "e.status <> 'closed'";
    elseif (in_array($status, ['new','assigned','replied','closed'], true)) { $where[] = 'e.status = ?'; $params[] = $status; }
    if (in_array($type, ['contact','consultation','viewing','tour','career'], true)) { $where[] = 'e.type = ?'; $params[] = $type; }
    if (input('mine') === '1') { $where[] = 'e.assigned_to = ?'; $params[] = current_user()['id']; }
    $sql = 'SELECT e.*, u.name AS assignee FROM enquiries e LEFT JOIN users u ON u.id=e.assigned_to';
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY (e.status="new") DESC, e.created_at DESC LIMIT 200';
    admin_view('inbox-index', ['title'=>'Inbox','rows'=>db_all($sql,$params),'status'=>$status,'type'=>$type]);
}

function show(string $id): void
{
    require_login(); require_can('inbox.view');
    $row = db_one('SELECT e.*, u.name AS assignee, l.title AS listing_title FROM enquiries e LEFT JOIN users u ON u.id=e.assigned_to LEFT JOIN listings l ON l.id=e.listing_id WHERE e.id=?', [(int)$id]);
    if (!$row) { http_response_code(404); admin_view('error',['code'=>404,'title'=>'Enquiry not found','message'=>'This enquiry does not exist.']); return; }
    if (!$row['read_at']) db_run('UPDATE enquiries SET read_at=? WHERE id=?',[now(),(int)$id]);
    admin_view('inbox-show',['title'=>'Enquiry from '.$row['name'],'enquiry'=>$row,'notes'=>db_all('SELECT * FROM enquiry_notes WHERE enquiry_id=? ORDER BY created_at',[(int)$id]),'users'=>db_all("SELECT id,name FROM users WHERE status='active' ORDER BY name")]);
}

function assign(string $id): never
{
    require_login(); require_can('inbox.manage');
    $userId=(int)input('assigned_to','0');
    if ($userId && !db_one("SELECT id FROM users WHERE id=? AND status='active'",[$userId])) { flash('error','Choose an active staff member.'); redirect(admin_url('inbox/'.$id)); }
    db_run("UPDATE enquiries SET assigned_to=?, status=IF(status='new','assigned',status) WHERE id=?",[$userId ?: null,(int)$id]);
    log_activity('assigned','enquiry',$id,'Assigned enquiry'); flash('success','Assignment saved.'); redirect(admin_url('inbox/'.$id));
}

function set_status(string $id): never
{
    require_login(); require_can('inbox.manage');
    $status=(string)input('status','');
    if (!in_array($status,['new','assigned','replied','closed'],true)) { flash('error','Unknown status.'); redirect(admin_url('inbox/'.$id)); }
    db_run('UPDATE enquiries SET status=?, replied_at=IF(?="replied",COALESCE(replied_at,?),replied_at), closed_at=IF(?="closed",COALESCE(closed_at,?),NULL) WHERE id=?',[$status,$status,now(),$status,now(),(int)$id]);
    log_activity('updated','enquiry',$id,'Marked enquiry '.$status); flash('success','Status updated.'); redirect(admin_url('inbox/'.$id));
}

function add_note(string $id): never
{
    require_login(); require_can('inbox.manage'); $body=(string)input('body','');
    if ($body==='') { flash('error','Write a note first.'); redirect(admin_url('inbox/'.$id)); }
    $u=current_user(); db_insert('enquiry_notes',['enquiry_id'=>(int)$id,'user_id'=>$u['id'],'user_name'=>$u['name'],'body'=>mb_substr($body,0,5000),'created_at'=>now()]);
    log_activity('noted','enquiry',$id,'Added an internal note'); flash('success','Note added.'); redirect(admin_url('inbox/'.$id));
}
