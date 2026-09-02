INSERT INTO pages (slug,name,title,meta_description,editable,sort) VALUES
('home','Home','DDREAM | Real Estate & Property Management in Ghana','Property sales, rentals and management for clients in Ghana and abroad.',1,0),
('about','About','About DDREAM','Meet DDREAM and learn how we manage Ghanaian property for local and diaspora clients.',1,1),
('virtual-tours','Virtual tours','Virtual Property Tours','View Ghanaian property remotely with a guided virtual tour.',1,2),
('careers','Careers','Careers at DDREAM','Explore opportunities to join the DDREAM team.',1,3),
('contact','Contact','Contact DDREAM','Speak with DDREAM about property sales, rentals, management or investment.',1,4),
('not-found','Not found','Page not found','The requested page could not be found.',1,5)
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO page_sections (page_id,section_key,name,type,data,sort,enabled)
SELECT p.id,s.section_key,s.name,'fields','{}',s.sort,1 FROM pages p JOIN (
 SELECT 'home' slug,'hero' section_key,'Hero' name,0 sort UNION ALL SELECT 'home','culturally-curated','Culturally curated',1 UNION ALL SELECT 'home','quick-links','Quick links',2 UNION ALL SELECT 'home','featured','Featured properties',3 UNION ALL SELECT 'home','diaspora','Diaspora support',4 UNION ALL SELECT 'home','commission','No client commission',5 UNION ALL SELECT 'home','services','Services',6 UNION ALL SELECT 'home','areas','Areas',7 UNION ALL SELECT 'home','insights','Insights',8 UNION ALL SELECT 'home','cta','Call to action',9 UNION ALL
 SELECT 'about','about-intro','Introduction',0 UNION ALL SELECT 'about','about-mission','Mission',1 UNION ALL SELECT 'about','about-objectives','Objectives',2 UNION ALL SELECT 'about','about-values','Values',3 UNION ALL SELECT 'about','about-services','Services',4 UNION ALL SELECT 'about','about-apart','What sets us apart',5 UNION ALL SELECT 'about','about-office','Office',6 UNION ALL SELECT 'about','cta','Call to action',7 UNION ALL
 SELECT 'virtual-tours','virtual-tours','Virtual tours content',0 UNION ALL SELECT 'virtual-tours','cta','Call to action',1 UNION ALL SELECT 'careers','careers','Careers content',0 UNION ALL SELECT 'contact','contact-details','Contact details',0 UNION ALL SELECT 'contact','contact-form','Contact form',1 UNION ALL SELECT 'not-found','not-found','Not-found content',0
) s ON s.slug=p.slug
ON DUPLICATE KEY UPDATE name=VALUES(name),sort=VALUES(sort);
