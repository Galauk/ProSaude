ALTER TABLE evento RENAME eve_hr_ini  TO eve_hr_ini2;
ALTER TABLE evento RENAME eve_hr_fim  TO eve_hr_fim2;




ALTER TABLE evento
   ADD COLUMN eve_hr_ini character(5);
   