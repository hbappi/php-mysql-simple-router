select c.*
from naz.category c
where (
		coalesce(:search_query, '') = ''
		or c.title like concat('%', :search_query, '%')
	)
	and (
		coalesce(:parent_category_id, -999) = -999
		or c.parent_category_id = :parent_category_id
	)
order by c.ordinal asc
limit :offset, :limit;