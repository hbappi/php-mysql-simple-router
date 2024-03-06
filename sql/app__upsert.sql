insert into naz.app (
        app_id,
        api_key,
        title,
        slug,
        description,
        deleted,
        ordinal,
        package_name
    )
values (
        :app_id,
        UUID(),
        :title,
        :slug,
        :description,
        coalesce(:deleted, 0),
        coalesce(:ordinal, 0),
        :package_name
    ) on duplicate key
update app_id = coalesce(:app_id, app_id),
    title = coalesce(:title, title),
    slug = coalesce(:slug, slug),
    description = coalesce(:description, description),
    deleted = coalesce(:deleted, deleted),
    ordinal = coalesce(:ordinal, ordinal),
    package_name = coalesce(:package_name, package_name)