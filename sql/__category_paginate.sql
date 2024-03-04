

SELECT 
	c.*
from
	naz.category c
where
	case
		when :search_query is not null then c.title like concat('%', :search_query,'%')
		else true
    end
order by
	c.ordinal asc
limit
	:offset, :limit
;