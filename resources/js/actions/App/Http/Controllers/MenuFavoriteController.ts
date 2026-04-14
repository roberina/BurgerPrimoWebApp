import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MenuFavoriteController::toggle
* @see app/Http/Controllers/MenuFavoriteController.php:13
* @route '/menu/{menuItem}/favorite'
*/
export const toggle = (args: { menuItem: number | { id: number } } | [menuItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/menu/{menuItem}/favorite',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MenuFavoriteController::toggle
* @see app/Http/Controllers/MenuFavoriteController.php:13
* @route '/menu/{menuItem}/favorite'
*/
toggle.url = (args: { menuItem: number | { id: number } } | [menuItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { menuItem: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { menuItem: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            menuItem: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        menuItem: typeof args.menuItem === 'object'
        ? args.menuItem.id
        : args.menuItem,
    }

    return toggle.definition.url
            .replace('{menuItem}', parsedArgs.menuItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MenuFavoriteController::toggle
* @see app/Http/Controllers/MenuFavoriteController.php:13
* @route '/menu/{menuItem}/favorite'
*/
toggle.post = (args: { menuItem: number | { id: number } } | [menuItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

const MenuFavoriteController = { toggle }

export default MenuFavoriteController