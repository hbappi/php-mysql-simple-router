-- delete from naz.category
-- where category_id = :category_id;
update naz.category
set deleted = 1
where category_id = :category_id;