// Shared types for Burger Builder

export interface Ingredient {
  id: number;
  name: string;
  name_en?: string | null;
  category: string;
  price: number;
  image?: string | null;
  is_available?: boolean;
}

export interface SelectedIngredient {
  id: number;
  quantity: number;
}

export interface IngredientPivot {
  quantity: number;
}

export interface BurgerIngredient extends Ingredient {
  pivot: IngredientPivot;
}

export type BurgerStatus = 'draft' | 'pending' | 'approved' | 'rejected';

export interface CustomBurger {
  id: number;
  name: string;
  description?: string | null;
  total_price: number;
  is_favorite: boolean;
  status: BurgerStatus;
  admin_note?: string | null;
  ingredients: BurgerIngredient[];
}

export interface Order {
  id: number;
  order_number: string;
  total_amount: number;
  status: string;
  created_at: string;
  customer_notes?: string | null;
  admin_notes?: string | null;
}