import dashboard from './dashboard'
import orders from './orders'
import menu from './menu'
import ingredients from './ingredients'

const admin = {
    dashboard: Object.assign(dashboard, dashboard),
    orders: Object.assign(orders, orders),
    menu: Object.assign(menu, menu),
    ingredients: Object.assign(ingredients, ingredients),
}

export default admin