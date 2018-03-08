<?php

namespace cmal\Api\Exception;

// BaseRouteMatched is only raised when no matching route was found but / was called
// Will be caught by NoRouteMatched in case you intended it to be not found
class BaseRouteMatched extends NoRouteMatched {}
