<?php

namespace App\Enums;

enum ProductType: string
{
    case HotDish = 'hot dish';
    case ColdDish = 'cold dish';
    case Drink = 'drink';
    case Dessert = 'dessert';
}
