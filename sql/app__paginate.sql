select *
from app
where (
        coalesce(:search_query, '') = ''
        or title like concat('%', :search_query, '%')
    )
    and deleted = 0
order by ordinal asc
limit :offset, :limit;