<?php

namespace src\middlewares;


/**
 * Base class for all middlewares
 * Request & Response is not stored as property to make object stateless & singleton (inspired by NestJS default singleton lifecycle).
 * Also inspired by ExpressJS middleware/route handler signature.
 */
abstract class Middleware {}
