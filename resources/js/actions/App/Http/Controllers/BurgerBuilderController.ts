import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BurgerBuilderController::index
* @see app/Http/Controllers/BurgerBuilderController.php:17
* @route '/burger-builder'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/burger-builder',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BurgerBuilderController::index
* @see app/Http/Controllers/BurgerBuilderController.php:17
* @route '/burger-builder'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BurgerBuilderController::index
* @see app/Http/Controllers/BurgerBuilderController.php:17
* @route '/burger-builder'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BurgerBuilderController::index
* @see app/Http/Controllers/BurgerBuilderController.php:17
* @route '/burger-builder'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BurgerBuilderController::store
* @see app/Http/Controllers/BurgerBuilderController.php:46
* @route '/burger-builder'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/burger-builder',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BurgerBuilderController::store
* @see app/Http/Controllers/BurgerBuilderController.php:46
* @route '/burger-builder'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BurgerBuilderController::store
* @see app/Http/Controllers/BurgerBuilderController.php:46
* @route '/burger-builder'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BurgerBuilderController::update
* @see app/Http/Controllers/BurgerBuilderController.php:86
* @route '/burger-builder/{burger}'
*/
export const update = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/burger-builder/{burger}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\BurgerBuilderController::update
* @see app/Http/Controllers/BurgerBuilderController.php:86
* @route '/burger-builder/{burger}'
*/
update.url = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { burger: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: typeof args.burger === 'object'
        ? args.burger.id
        : args.burger,
    }

    return update.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BurgerBuilderController::update
* @see app/Http/Controllers/BurgerBuilderController.php:86
* @route '/burger-builder/{burger}'
*/
update.put = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BurgerBuilderController::destroy
* @see app/Http/Controllers/BurgerBuilderController.php:119
* @route '/burger-builder/{burger}'
*/
export const destroy = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/burger-builder/{burger}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BurgerBuilderController::destroy
* @see app/Http/Controllers/BurgerBuilderController.php:119
* @route '/burger-builder/{burger}'
*/
destroy.url = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { burger: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: typeof args.burger === 'object'
        ? args.burger.id
        : args.burger,
    }

    return destroy.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BurgerBuilderController::destroy
* @see app/Http/Controllers/BurgerBuilderController.php:119
* @route '/burger-builder/{burger}'
*/
destroy.delete = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BurgerBuilderController::toggleFavorite
* @see app/Http/Controllers/BurgerBuilderController.php:128
* @route '/burger-builder/{burger}/favorite'
*/
export const toggleFavorite = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleFavorite.url(args, options),
    method: 'post',
})

toggleFavorite.definition = {
    methods: ["post"],
    url: '/burger-builder/{burger}/favorite',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BurgerBuilderController::toggleFavorite
* @see app/Http/Controllers/BurgerBuilderController.php:128
* @route '/burger-builder/{burger}/favorite'
*/
toggleFavorite.url = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { burger: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: typeof args.burger === 'object'
        ? args.burger.id
        : args.burger,
    }

    return toggleFavorite.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BurgerBuilderController::toggleFavorite
* @see app/Http/Controllers/BurgerBuilderController.php:128
* @route '/burger-builder/{burger}/favorite'
*/
toggleFavorite.post = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleFavorite.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BurgerBuilderController::submitForReview
* @see app/Http/Controllers/BurgerBuilderController.php:139
* @route '/burger-builder/{burger}/submit'
*/
export const submitForReview = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitForReview.url(args, options),
    method: 'post',
})

submitForReview.definition = {
    methods: ["post"],
    url: '/burger-builder/{burger}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BurgerBuilderController::submitForReview
* @see app/Http/Controllers/BurgerBuilderController.php:139
* @route '/burger-builder/{burger}/submit'
*/
submitForReview.url = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { burger: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { burger: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            burger: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        burger: typeof args.burger === 'object'
        ? args.burger.id
        : args.burger,
    }

    return submitForReview.definition.url
            .replace('{burger}', parsedArgs.burger.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BurgerBuilderController::submitForReview
* @see app/Http/Controllers/BurgerBuilderController.php:139
* @route '/burger-builder/{burger}/submit'
*/
submitForReview.post = (args: { burger: number | { id: number } } | [burger: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitForReview.url(args, options),
    method: 'post',
})

const BurgerBuilderController = { index, store, update, destroy, toggleFavorite, submitForReview }

export default BurgerBuilderController