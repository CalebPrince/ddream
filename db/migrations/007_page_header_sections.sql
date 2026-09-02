-- ---------------------------------------------------------------------------
-- 007 The interior-page headers become editable bands of their own, so the
-- eyebrow, heading, lead, photograph and figures at the top of About, Virtual
-- tours, Careers and Contact can be edited like every other section.
-- ---------------------------------------------------------------------------

INSERT INTO page_sections (page_id,section_key,name,type,data,sort,enabled)
SELECT p.id,s.section_key,s.name,'fields','{}',s.sort,1 FROM pages p JOIN (
 SELECT 'about' slug,'about-hero' section_key,'Page header' name,-1 sort UNION ALL
 SELECT 'virtual-tours','virtual-tours-hero','Page header',-1 UNION ALL
 SELECT 'careers','careers-hero','Page header',-1 UNION ALL
 SELECT 'contact','contact-hero','Page header',-1
) s ON s.slug=p.slug
ON DUPLICATE KEY UPDATE name=VALUES(name),sort=VALUES(sort);
