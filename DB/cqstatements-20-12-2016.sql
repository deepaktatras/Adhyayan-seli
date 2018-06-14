ALTER TABLE `d_core_question_stmt` 
CHANGE COLUMN `statement` `statement` TEXT NOT NULL COMMENT '' ;

ALTER TABLE `d_core_question_stmt` 
ENGINE = InnoDB ,
ADD PRIMARY KEY (`core_question_id`)  COMMENT '';
ALTER TABLE `d_core_question_stmt` 
ADD CONSTRAINT `fkk_d_core_question`
  FOREIGN KEY (`core_question_id`)
  REFERENCES `d_core_question` (`core_question_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
ALTER TABLE `d_key_question_stmt` 
ADD PRIMARY KEY (`key_question_id`)  COMMENT '';
  

UPDATE `d_core_question_stmt` SET `statement`='The school\'s leadership promotes the voice of the staff and students while giving them opportunities for reflection and self-scrutiny to create a sense of purpose in their work and personal lives and be guided by their conscience.' WHERE `core_question_id`='244';

UPDATE `d_core_question_stmt` SET `statement`='Staff and students are encouraged to explore, collaborate and discuss aspects of the curriculum regularly. The school ensures that all children are involved in sporting and cultural activities and have opportunities to develop their leadership qualities and contribute positively to the school. They are also encouraged to discuss ideas and events collaboratively as part of their learning.' WHERE `core_question_id`='245';

UPDATE `d_core_question_stmt` SET `statement`='Teachers and students have opportunities for reflection, discussion and research using mass media constructively to explore spiritual, moral and social values, and to develop students as independent learners, thinkers, problem solvers and communicators.' WHERE `core_question_id`='247';

UPDATE `d_core_question_stmt` SET `statement`='The school provides opportunities for teams of students to develop inclusive projects, serving the less privileged in the local community, and involving parents to enhance student leadership skills and promote community partnerships.' WHERE `core_question_id`='248';

UPDATE `d_core_question_stmt` SET `statement`='The school ensures that students supported by alumni, teachers and parents have opportunities to realise students\' inner potential, make the study of diversity real while implementing policies and practice on inclusion and social justice as a part of the curriculum.' WHERE `core_question_id`='250';

UPDATE `d_core_question_stmt` SET `statement`='The school\'s leadership welcomes student voice in working for peace and provides them with opportunities to volunteer with organisations committed to social justice and inclusion.  They provide access to learning, social and emotional support for all their students to address their diverse needs.' WHERE `core_question_id`='252';

UPDATE `d_core_question_stmt` SET `statement`='Leaders and teachers act as guides and role model, facilitate their students\' learning, promote team work, and help them to navigate the world wide web(www) for the purposes of research and enquiry.' WHERE `core_question_id`='253';

UPDATE `d_core_question_stmt` SET `statement`='The educational community demonstrates love and care for all members and encourages students to undertake projects to improve the life of community whilst providing opportunities for reflection and evaluation to embrace change.' WHERE `core_question_id`='254';

UPDATE `d_core_question_stmt` SET `statement`='The school\'s leadership team ensures students have access to a rich, wide ranging curriculum that values the arts and culture, evaluates school\'s and student\'s perfl6ormance, and promotes a collaborative learning environment.' WHERE `core_question_id`='255';

-- salesian
UPDATE `d_core_question_stmt` SET `statement`='Students benefit from a planned curriculum which provides them with rich and varied learning experience underpinned by ethical guidelines and collaboration.' WHERE `core_question_id`='58';

UPDATE `d_core_question_stmt` SET `statement`='The school\'s pervasive rapport is enhanced by the Salesian role modelling of relationships building which contributes to its caring and secure community.' WHERE `core_question_id`='59';

UPDATE `d_core_question_stmt` SET `statement`='3.	The school’s behaviour and discipline policies in keeping with Preventive System are implemented with firmness and flexibility and where correcting fault is seen as a constructive step towards conscience building.' WHERE `core_question_id`='60';

UPDATE `d_core_question_stmt` SET `statement`='The school\'s family spirit is evidence of a community marked by trust and acceptance, enriched by its values, which creatively sustains and develops the enthusiasm, initiatives and talents of students.  ' WHERE `core_question_id`='55';

UPDATE `d_core_question_stmt` SET `statement`='The school caters to the religious needs of the students with teaching which promotes respect, duty and service, and prepares them with values and skills necessary for 21<sup>st</sup> century.' WHERE `core_question_id`='56';

UPDATE `d_core_question_stmt` SET `statement`='The educator\'s presence which helps students feel known, enthuses them to reach new horizons, is underpinned by his holiness and sanctity.' WHERE `core_question_id`='57';

UPDATE `d_core_question_stmt` SET `statement`='The school promotes the enrolment of local disadvantaged students and equips them with leadership skills and in addition provides them vocational and career guidance.' WHERE `core_question_id`='61';

UPDATE `d_core_question_stmt` SET `statement`='The Salesian community encourages and involves staff, parents and students to participate in and co-ordinate projects which support the poor and disadvantaged children of the neighbourhood.' WHERE `core_question_id`='62';

UPDATE `d_core_question_stmt` SET `statement`='The Salesians in the school provide students with spiritual/vocational direction, helps them to reflect on their relationships, attitude, choices and behaviours and prepare them to become agents of social change as \'good and honest citizens\'.  ' WHERE `core_question_id`='63';

UPDATE `d_core_question_stmt` SET `statement`='The school\'s behaviour and discipline policies in keeping with Preventive System are implemented with firmness and flexibility and where correcting fault is seen as a constructive step towards conscience building.' WHERE `core_question_id`='60';

UPDATE `d_core_question_stmt` SET `statement`='Staff and students are encouraged to explore, collaborate and discuss aspects of the curriculum regularly.  The school ensures that all children are involved in sporting and cultural activities and have opportunities to develop their leadership qualities and contribute positively to the school.  They are also encouraged to discuss ideas and events collaboratively as part of their learning.' WHERE `core_question_id`='245';

UPDATE `d_core_question_stmt` SET `statement`='Staff and students are encouraged to explore, collaborate and discuss aspects of the curriculum regularly.  The school ensures that all children are involved in sporting and cultural activities and have opportunities to develop their leadership qualities and contribute positively to the school.  They are also encouraged to discuss ideas and events collaboratively as part of their learning.' WHERE `core_question_id`='245';


