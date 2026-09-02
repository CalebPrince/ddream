-- ---------------------------------------------------------------------------
-- 005 Seed the settings rows the admin edits.
-- Values here are defaults; staff change them from Settings.
-- ---------------------------------------------------------------------------

INSERT INTO settings (group_name, setting_key, value, type, label, hint, sort, updated_at) VALUES
('maintenance', 'maintenance_enabled', '0', 'bool',
 'Maintenance mode',
 'Closes the public site to visitors. Signed-in staff still see everything.', 0, NOW()),

('maintenance', 'maintenance_heading', 'We are making some improvements', 'text',
 'Heading',
 'The large line on the maintenance page.', 1, NOW()),

('maintenance', 'maintenance_message',
 'The DDREAM website is briefly offline while we update it. Please try again shortly. If your enquiry cannot wait, call or send us a WhatsApp message and we will pick it up.',
 'textarea',
 'Message',
 'A short explanation. Give people a way to reach you.', 2, NOW()),

('maintenance', 'maintenance_back_at', '', 'text',
 'Expected back',
 'Optional, for example "later this afternoon". Left blank, nothing is shown.', 3, NOW());
