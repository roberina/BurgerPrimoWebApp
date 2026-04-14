import AddonItemController from './AddonItemController'
import DashboardController from './DashboardController'
import OrderController from './OrderController'
import BurgerReviewController from './BurgerReviewController'
import MenuCategoryController from './MenuCategoryController'
import MenuItemController from './MenuItemController'
import IngredientController from './IngredientController'
import ReviewController from './ReviewController'
import AnnouncementController from './AnnouncementController'

const Admin = {
    AddonItemController: Object.assign(AddonItemController, AddonItemController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    OrderController: Object.assign(OrderController, OrderController),
    BurgerReviewController: Object.assign(BurgerReviewController, BurgerReviewController),
    MenuCategoryController: Object.assign(MenuCategoryController, MenuCategoryController),
    MenuItemController: Object.assign(MenuItemController, MenuItemController),
    IngredientController: Object.assign(IngredientController, IngredientController),
    ReviewController: Object.assign(ReviewController, ReviewController),
    AnnouncementController: Object.assign(AnnouncementController, AnnouncementController),
}

export default Admin