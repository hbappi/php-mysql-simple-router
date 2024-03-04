

SELECT 
	c.*
from
	naz.category c
where
	(:search_query = '' OR c.title LIKE CONCAT('%', :search_query, '%'))
order by
	c.ordinal asc
limit
	:offset, :limit
;