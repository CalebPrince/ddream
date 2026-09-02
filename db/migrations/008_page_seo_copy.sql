-- ---------------------------------------------------------------------------
-- 008 Page contents now supplies the <title> and meta description, so the rows
-- need to hold the wording the routes were serving rather than the placeholder
-- text seeded in 006. Only rows still carrying that placeholder are touched, so
-- anything already edited in the admin is left alone.
-- ---------------------------------------------------------------------------

UPDATE pages SET
    title = 'DDREAM | Real estate for Ghana and the diaspora',
    meta_description = 'DDREAM is a Ghanaian real estate management company connecting domestic and diaspora clients with verified property to buy, rent, build and manage, on a no client commission basis.'
WHERE slug = 'home' AND title = 'DDREAM | Real Estate & Property Management in Ghana';

UPDATE pages SET
    title = 'About us | DDREAM',
    meta_description = 'A Ghanaian real estate solutions company bridging property owners, investors and home seekers in Ghana with Ghanaians across the diaspora. Our mission, vision, objectives and the no client commission promise.'
WHERE slug = 'about' AND title = 'About DDREAM';

UPDATE pages SET
    title = 'Virtual property tours in Ghana | DDREAM',
    meta_description = 'Live video walkthroughs of Ghanaian property, led by you, with a DDREAM adviser on site holding the camera. Free, booked within 48 hours.'
WHERE slug = 'virtual-tours' AND title = 'Virtual Property Tours';

UPDATE pages SET
    title = 'Careers | DDREAM',
    meta_description = 'Work at DDREAM in Accra: property advisers, property managers, construction supervisors and client liaison for our diaspora desk.'
WHERE slug = 'careers' AND title = 'Careers at DDREAM';

UPDATE pages SET
    title = 'Contact us | DDREAM',
    meta_description = 'Talk to a DDREAM adviser about buying, renting, letting, building or managing property in Ghana. Office in Accra, replies within one working day, and no client commission.'
WHERE slug = 'contact' AND title = 'Contact DDREAM';

UPDATE pages SET
    title = 'Page not found | DDREAM',
    meta_description = 'That page does not exist yet. Search our listings or talk to an adviser.'
WHERE slug = 'not-found' AND title = 'Page not found';
