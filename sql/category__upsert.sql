insert into category (
        category_id,
        title,
        child_type,
        ordinal,
        parent_category_id
    )
values (
        :category_id,
        :title,
        :child_type,
        coalesce(:ordinal, 0),
        coalesce(:parent_category_id, -1)
    ) on duplicate key
update title = coalesce(:title, title),
    child_type = coalesce(:child_type, child_type),
    ordinal = coalesce(:ordinal, ordinal),
    parent_category_id = coalesce(
        :parent_category_id,
        parent_category_id
    );