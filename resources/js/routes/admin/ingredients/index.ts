import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\IngredientController::index
* @see app/Http/Controllers/Admin/IngredientController.php:16
* @route '/admin/ingredients'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/ingredients',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\IngredientController::index
* @see app/Http/Controllers/Admin/IngredientController.php:16
* @route '/admin/ingredients'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\IngredientController::index
* @see app/Http/Controllers/Admin/IngredientController.php:16
* @route '/admin/ingredients'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::index
* @see app/Http/Controllers/Admin/IngredientController.php:16
* @route '/admin/ingredients'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::create
* @see app/Http/Controllers/Admin/IngredientController.php:41
* @route '/admin/ingredients/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/ingredients/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\IngredientController::create
* @see app/Http/Controllers/Admin/IngredientController.php:41
* @route '/admin/ingredients/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\IngredientController::create
* @see app/Http/Controllers/Admin/IngredientController.php:41
* @route '/admin/ingredients/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::create
* @see app/Http/Controllers/Admin/IngredientController.php:41
* @route '/admin/ingredients/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::store
* @see app/Http/Controllers/Admin/IngredientController.php:49
* @route '/admin/ingredients'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/ingredients',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\IngredientController::store
* @see app/Http/Controllers/Admin/IngredientController.php:49
* @route '/admin/ingredients'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\IngredientController::store
* @see app/Http/Controllers/Admin/IngredientController.php:49
* @route '/admin/ingredients'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::edit
* @see app/Http/Controllers/Admin/IngredientController.php:67
* @route '/admin/ingredients/{ingredient}/edit'
*/
export const edit = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/ingredients/{ingredient}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\IngredientController::edit
* @see app/Http/Controllers/Admin/IngredientController.php:67
* @route '/admin/ingredients/{ingredient}/edit'
*/
edit.url = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return edit.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\IngredientController::edit
* @see app/Http/Controllers/Admin/IngredientController.php:67
* @route '/admin/ingredients/{ingredient}/edit'
*/
edit.get = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::edit
* @see app/Http/Controllers/Admin/IngredientController.php:67
* @route '/admin/ingredients/{ingredient}/edit'
*/
edit.head = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::update
* @see app/Http/Controllers/Admin/IngredientController.php:77
* @route '/admin/ingredients/{ingredient}'
*/
export const update = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/ingredients/{ingredient}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\IngredientController::update
* @see app/Http/Controllers/Admin/IngredientController.php:77
* @route '/admin/ingredients/{ingredient}'
*/
update.url = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return update.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\IngredientController::update
* @see app/Http/Controllers/Admin/IngredientController.php:77
* @route '/admin/ingredients/{ingredient}'
*/
update.put = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::destroy
* @see app/Http/Controllers/Admin/IngredientController.php:95
* @route '/admin/ingredients/{ingredient}'
*/
export const destroy = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/ingredients/{ingredient}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\IngredientController::destroy
* @see app/Http/Controllers/Admin/IngredientController.php:95
* @route '/admin/ingredients/{ingredient}'
*/
destroy.url = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return destroy.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\IngredientController::destroy
* @see app/Http/Controllers/Admin/IngredientController.php:95
* @route '/admin/ingredients/{ingredient}'
*/
destroy.delete = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\IngredientController::toggle
* @see app/Http/Controllers/Admin/IngredientController.php:106
* @route '/admin/ingredients/{ingredient}/toggle'
*/
export const toggle = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/admin/ingredients/{ingredient}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\IngredientController::toggle
* @see app/Http/Controllers/Admin/IngredientController.php:106
* @route '/admin/ingredients/{ingredient}/toggle'
*/
toggle.url = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ingredient: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ingredient: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ingredient: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ingredient: typeof args.ingredient === 'object'
        ? args.ingredient.id
        : args.ingredient,
    }

    return toggle.definition.url
            .replace('{ingredient}', parsedArgs.ingredient.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\IngredientController::toggle
* @see app/Http/Controllers/Admin/IngredientController.php:106
* @route '/admin/ingredients/{ingredient}/toggle'
*/
toggle.post = (args: { ingredient: number | { id: number } } | [ingredient: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

const ingredients = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    toggle: Object.assign(toggle, toggle),
}

export default ingredients