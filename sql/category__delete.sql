-- delete from category
-- where category_id = :category_id;
update category
set deleted = 1
where category_id = :category_id;