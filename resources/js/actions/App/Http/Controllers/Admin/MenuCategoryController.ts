import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::index
* @see app/Http/Controllers/Admin/MenuCategoryController.php:13
* @route '/admin/menu/categories'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/menu/categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::index
* @see app/Http/Controllers/Admin/MenuCategoryController.php:13
* @route '/admin/menu/categories'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::index
* @see app/Http/Controllers/Admin/MenuCategoryController.php:13
* @route '/admin/menu/categories'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::index
* @see app/Http/Controllers/Admin/MenuCategoryController.php:13
* @route '/admin/menu/categories'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::create
* @see app/Http/Controllers/Admin/MenuCategoryController.php:24
* @route '/admin/menu/categories/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/menu/categories/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::create
* @see app/Http/Controllers/Admin/MenuCategoryController.php:24
* @route '/admin/menu/categories/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::create
* @see app/Http/Controllers/Admin/MenuCategoryController.php:24
* @route '/admin/menu/categories/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::create
* @see app/Http/Controllers/Admin/MenuCategoryController.php:24
* @route '/admin/menu/categories/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::store
* @see app/Http/Controllers/Admin/MenuCategoryController.php:30
* @route '/admin/menu/categories'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/menu/categories',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::store
* @see app/Http/Controllers/Admin/MenuCategoryController.php:30
* @route '/admin/menu/categories'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::store
* @see app/Http/Controllers/Admin/MenuCategoryController.php:30
* @route '/admin/menu/categories'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::edit
* @see app/Http/Controllers/Admin/MenuCategoryController.php:47
* @route '/admin/menu/categories/{category}/edit'
*/
export const edit = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/menu/categories/{category}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::edit
* @see app/Http/Controllers/Admin/MenuCategoryController.php:47
* @route '/admin/menu/categories/{category}/edit'
*/
edit.url = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { category: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            category: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        category: typeof args.category === 'object'
        ? args.category.id
        : args.category,
    }

    return edit.definition.url
            .replace('{category}', parsedArgs.category.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::edit
* @see app/Http/Controllers/Admin/MenuCategoryController.php:47
* @route '/admin/menu/categories/{category}/edit'
*/
edit.get = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::edit
* @see app/Http/Controllers/Admin/MenuCategoryController.php:47
* @route '/admin/menu/categories/{category}/edit'
*/
edit.head = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::update
* @see app/Http/Controllers/Admin/MenuCategoryController.php:55
* @route '/admin/menu/categories/{category}'
*/
export const update = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/menu/categories/{category}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::update
* @see app/Http/Controllers/Admin/MenuCategoryController.php:55
* @route '/admin/menu/categories/{category}'
*/
update.url = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { category: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            category: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        category: typeof args.category === 'object'
        ? args.category.id
        : args.category,
    }

    return update.definition.url
            .replace('{category}', parsedArgs.category.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::update
* @see app/Http/Controllers/Admin/MenuCategoryController.php:55
* @route '/admin/menu/categories/{category}'
*/
update.put = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::destroy
* @see app/Http/Controllers/Admin/MenuCategoryController.php:72
* @route '/admin/menu/categories/{category}'
*/
export const destroy = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/menu/categories/{category}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::destroy
* @see app/Http/Controllers/Admin/MenuCategoryController.php:72
* @route '/admin/menu/categories/{category}'
*/
destroy.url = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { category: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            category: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        category: typeof args.category === 'object'
        ? args.category.id
        : args.category,
    }

    return destroy.definition.url
            .replace('{category}', parsedArgs.category.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::destroy
* @see app/Http/Controllers/Admin/MenuCategoryController.php:72
* @route '/admin/menu/categories/{category}'
*/
destroy.delete = (args: { category: number | { id: number } } | [category: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::updateOrder
* @see app/Http/Controllers/Admin/MenuCategoryController.php:81
* @route '/admin/menu/categories/update-order'
*/
export const updateOrder = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrder.url(options),
    method: 'post',
})

updateOrder.definition = {
    methods: ["post"],
    url: '/admin/menu/categories/update-order',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::updateOrder
* @see app/Http/Controllers/Admin/MenuCategoryController.php:81
* @route '/admin/menu/categories/update-order'
*/
updateOrder.url = (options?: RouteQueryOptions) => {
    return updateOrder.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MenuCategoryController::updateOrder
* @see app/Http/Controllers/Admin/MenuCategoryController.php:81
* @route '/admin/menu/categories/update-order'
*/
updateOrder.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateOrder.url(options),
    method: 'post',
})

const MenuCategoryController = { index, create, store, edit, update, destroy, updateOrder }

export default MenuCategoryController