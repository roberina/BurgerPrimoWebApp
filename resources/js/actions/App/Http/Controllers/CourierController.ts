import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CourierController::track
* @see app/Http/Controllers/CourierController.php:17
* @route '/courier/track/{token}'
*/
export const track = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: track.url(args, options),
    method: 'get',
})

track.definition = {
    methods: ["get","head"],
    url: '/courier/track/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CourierController::track
* @see app/Http/Controllers/CourierController.php:17
* @route '/courier/track/{token}'
*/
track.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return track.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourierController::track
* @see app/Http/Controllers/CourierController.php:17
* @route '/courier/track/{token}'
*/
track.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: track.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CourierController::track
* @see app/Http/Controllers/CourierController.php:17
* @route '/courier/track/{token}'
*/
track.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: track.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CourierController::updateLocation
* @see app/Http/Controllers/CourierController.php:81
* @route '/courier/location/{token}'
*/
export const updateLocation = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateLocation.url(args, options),
    method: 'post',
})

updateLocation.definition = {
    methods: ["post"],
    url: '/courier/location/{token}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourierController::updateLocation
* @see app/Http/Controllers/CourierController.php:81
* @route '/courier/location/{token}'
*/
updateLocation.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return updateLocation.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourierController::updateLocation
* @see app/Http/Controllers/CourierController.php:81
* @route '/courier/location/{token}'
*/
updateLocation.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateLocation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CourierController::accept
* @see app/Http/Controllers/CourierController.php:49
* @route '/courier/accept/{token}'
*/
export const accept = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accept.url(args, options),
    method: 'post',
})

accept.definition = {
    methods: ["post"],
    url: '/courier/accept/{token}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourierController::accept
* @see app/Http/Controllers/CourierController.php:49
* @route '/courier/accept/{token}'
*/
accept.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return accept.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourierController::accept
* @see app/Http/Controllers/CourierController.php:49
* @route '/courier/accept/{token}'
*/
accept.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accept.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CourierController::decline
* @see app/Http/Controllers/CourierController.php:61
* @route '/courier/decline/{token}'
*/
export const decline = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: decline.url(args, options),
    method: 'post',
})

decline.definition = {
    methods: ["post"],
    url: '/courier/decline/{token}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CourierController::decline
* @see app/Http/Controllers/CourierController.php:61
* @route '/courier/decline/{token}'
*/
decline.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return decline.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CourierController::decline
* @see app/Http/Controllers/CourierController.php:61
* @route '/courier/decline/{token}'
*/
decline.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: decline.url(args, options),
    method: 'post',
})

const CourierController = { track, updateLocation, accept, decline }

export default CourierController