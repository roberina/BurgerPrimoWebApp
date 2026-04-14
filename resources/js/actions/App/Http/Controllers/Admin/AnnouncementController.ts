import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AnnouncementController::index
* @see app/Http/Controllers/Admin/AnnouncementController.php:12
* @route '/admin/announcements'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/announcements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::index
* @see app/Http/Controllers/Admin/AnnouncementController.php:12
* @route '/admin/announcements'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::index
* @see app/Http/Controllers/Admin/AnnouncementController.php:12
* @route '/admin/announcements'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::index
* @see app/Http/Controllers/Admin/AnnouncementController.php:12
* @route '/admin/announcements'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::store
* @see app/Http/Controllers/Admin/AnnouncementController.php:19
* @route '/admin/announcements'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/announcements',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::store
* @see app/Http/Controllers/Admin/AnnouncementController.php:19
* @route '/admin/announcements'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::store
* @see app/Http/Controllers/Admin/AnnouncementController.php:19
* @route '/admin/announcements'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::update
* @see app/Http/Controllers/Admin/AnnouncementController.php:26
* @route '/admin/announcements/{announcement}'
*/
export const update = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/announcements/{announcement}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::update
* @see app/Http/Controllers/Admin/AnnouncementController.php:26
* @route '/admin/announcements/{announcement}'
*/
update.url = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { announcement: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { announcement: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            announcement: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        announcement: typeof args.announcement === 'object'
        ? args.announcement.id
        : args.announcement,
    }

    return update.definition.url
            .replace('{announcement}', parsedArgs.announcement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::update
* @see app/Http/Controllers/Admin/AnnouncementController.php:26
* @route '/admin/announcements/{announcement}'
*/
update.put = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::destroy
* @see app/Http/Controllers/Admin/AnnouncementController.php:33
* @route '/admin/announcements/{announcement}'
*/
export const destroy = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/announcements/{announcement}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::destroy
* @see app/Http/Controllers/Admin/AnnouncementController.php:33
* @route '/admin/announcements/{announcement}'
*/
destroy.url = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { announcement: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { announcement: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            announcement: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        announcement: typeof args.announcement === 'object'
        ? args.announcement.id
        : args.announcement,
    }

    return destroy.definition.url
            .replace('{announcement}', parsedArgs.announcement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::destroy
* @see app/Http/Controllers/Admin/AnnouncementController.php:33
* @route '/admin/announcements/{announcement}'
*/
destroy.delete = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::toggle
* @see app/Http/Controllers/Admin/AnnouncementController.php:39
* @route '/admin/announcements/{announcement}/toggle'
*/
export const toggle = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/admin/announcements/{announcement}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::toggle
* @see app/Http/Controllers/Admin/AnnouncementController.php:39
* @route '/admin/announcements/{announcement}/toggle'
*/
toggle.url = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { announcement: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { announcement: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            announcement: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        announcement: typeof args.announcement === 'object'
        ? args.announcement.id
        : args.announcement,
    }

    return toggle.definition.url
            .replace('{announcement}', parsedArgs.announcement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AnnouncementController::toggle
* @see app/Http/Controllers/Admin/AnnouncementController.php:39
* @route '/admin/announcements/{announcement}/toggle'
*/
toggle.post = (args: { announcement: number | { id: number } } | [announcement: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

const AnnouncementController = { index, store, update, destroy, toggle }

export default AnnouncementController