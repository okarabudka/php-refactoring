<?php

namespace src\refactors\task2\after\shippingType;

enum ShippingStrategy: string
{
    case Standard = 'standard';
    case Express = 'express';
    case Overnight = 'overnight';
}
