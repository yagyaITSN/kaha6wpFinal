<!DOCTYPE html>
<html lang="en-US">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Kaha6 - Nepal's Business Directory</title>
   <meta name="description"
      content="Kaha6 is Nepal's premier business directory platform, connecting customers with local businesses.">
   <meta name="keywords" content="Kaha6, Business Directory, Nepal, Local Businesses, Business Listings">
   <!-- Bootstrap 5 CSS -->
   <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
   <!-- Font Awesome -->
   <link rel="stylesheet" href="./assets/css/common.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body>
   <!-- Header Navigation -->
   <header>
      <nav class="navbar navbar-expand-lg">
         <div class="container">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="./index.php">
               <img src="https://kaha6.com/wp-content/uploads/logo-44.png" alt="KAHA6 Logo" height="40">
            </a>

            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas"
               data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas offcanvas-end offcanvas-nav" tabindex="-1" id="offcanvasNavbar">
               <div class="offcanvas-header">
                  <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas"
                     aria-label="Close"></button>
               </div>
               <div class="offcanvas-body text-dark">
                  <ul class="navbar-nav justify-content-end text-center gap-3 flex-grow-1">
                     <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="./index.php">Home</a>
                     </li>
                     <!-- Mega Menu Item -->
                     <li class="nav-item dropdown dropdown-mega position-static">
                        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false" id="megaMenuDropdown">
                           Business Type
                        </a>
                        <div class="dropdown-menu w-100 mt-0 border-top-0 rounded-0 shadow-lg">
                           <div class="container py-4">
                              <div class="row">
                                 <!-- Column 1 -->
                                 <div class="col-lg-3 mb-3 mb-lg-0">
                                    <h6 class="dropdown-header">General Services</h6>
                                    <ul class="list-unstyled mb-0">
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-tractor me-2"></i>Agriculture</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-tshirt me-2"></i>Apparels & Footwear</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-car me-2"></i>Automobile</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-university me-2"></i>Banks and Finance</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-scissors me-2"></i>Beauty and Salons</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-spa me-2"></i>Beauty and Spa</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-briefcase me-2"></i>Business Services</a>
                                       </li>
                                    </ul>
                                 </div>
                                 <!-- Column 2 -->
                                 <div class="col-lg-3 mb-3 mb-lg-0">
                                    <h6 class="dropdown-header">Commercial & Public</h6>
                                    <ul class="list-unstyled mb-0">
                                       <li class="dropdown-submenu">
                                          <a class="dropdown-item dropdown-toggle" href="#"
                                             data-bs-toggle="dropdown" aria-expanded="false">
                                             <i class="fas fa-graduation-cap me-2"></i>Education
                                          </a>
                                          <ul class="dropdown-menu">
                                             <li><a class="dropdown-item" href="#">Primary Education</a>
                                             </li>
                                             <li><a class="dropdown-item" href="#">Secondary
                                                   Education</a>
                                             </li>
                                             <li><a class="dropdown-item" href="#">Higher Education</a>
                                             </li>
                                             <li><a class="dropdown-item" href="#">Vocational
                                                   Training</a>
                                             </li>
                                          </ul>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-hard-hat me-2"></i>Construction</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-truck me-2"></i>Delivery Services</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-film me-2"></i>Entertainment</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-industry me-2"></i>Factory</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-utensils me-2"></i>Food</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-couch me-2"></i>Furniture</a></li>
                                    </ul>
                                 </div>
                                 <!-- Column 3 -->
                                 <div class="col-lg-3 mb-3 mb-lg-0">
                                    <h6 class="dropdown-header">Professional & Healthcare</h6>
                                    <ul class="list-unstyled mb-0">
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-gem me-2"></i>Gems & Jewellery</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-hospital me-2"></i>Health & Medicine</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-bed me-2"></i>Hostel</a></li>
                                       <li class="dropdown-submenu">
                                          <a class="dropdown-item dropdown-toggle" href="#"
                                             data-bs-toggle="dropdown" aria-expanded="false">
                                             <i class="fas fa-laptop-code me-2"></i>IT Services
                                          </a>
                                          <ul class="dropdown-menu">
                                             <li><a class="dropdown-item" href="#">Web Development</a>
                                             </li>
                                             <li><a class="dropdown-item" href="#">Mobile App
                                                   Development</a>
                                             </li>
                                             <li><a class="dropdown-item" href="#">Software Company</a>
                                             </li>
                                             <li><a class="dropdown-item" href="#">Networking</a></li>
                                             <li><a class="dropdown-item" href="#">Computer Hardware</a>
                                             </li>
                                          </ul>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-tshirt me-2"></i>Laundromat</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-gavel me-2"></i>Law Firm</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-wine-glass-alt me-2"></i>Liquor &
                                             Tobacco</a>
                                       </li>
                                    </ul>
                                 </div>
                                 <!-- Column 4 -->
                                 <div class="col-lg-3">
                                    <h6 class="dropdown-header">Retail & Others</h6>
                                    <ul class="list-unstyled mb-0">
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-store me-2"></i>Market</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-newspaper me-2"></i>Media</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-hands-helping me-2"></i>Nonprofit
                                             Organization</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-users me-2"></i>Organization</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-tree me-2"></i>Park</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-pen-fancy me-2"></i>Pen Manufacturing</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-dumbbell me-2"></i>Physical Fitness</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-beer me-2"></i>Pub & Bar</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-home me-2"></i>Real Estate</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-shopping-bag me-2"></i>Retail Shopping</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-store-alt me-2"></i>Shopping Mall</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-running me-2"></i>Sports & Recreation</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-chart-line me-2"></i>Stock Brokerage</a>
                                       </li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-bus me-2"></i>Travel & Transport</a></li>
                                       <li><a class="dropdown-item" href="#"><i
                                                class="fas fa-ellipsis-h me-2"></i>Others</a></li>
                                    </ul>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="./page-blog.php">Blog</a>
                     </li>
                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                           Info
                        </a>
                        <ul class="dropdown-menu">
                           <li><a class="dropdown-item" href="./page-about.php"><i
                                    class="fas fa-info-circle me-2"></i>About</a></li>
                           <li><a class="dropdown-item" href="./page-privacy-policy.php"><i
                                    class="fas fa-shield-alt me-2"></i>Privacy Policy</a></li>
                           <li><a class="dropdown-item" href="./page-faq.php"><i
                                    class="fas fa-question-circle me-2"></i>FAQ</a></li>
                           <li><a class="dropdown-item" href="./page-contact.php"><i
                                    class="fas fa-user me-2"></i>Contact</a></li>
                        </ul>
                     </li>
                     <li class="nav-item">
                        <a href="./page-register.php" class="btn btn-custom-red">
                           Add Your Business
                        </a>
                     </li>
                  </ul>
                  <!-- User Menu -->
                  <div class="user__menu-icon dropdown d-lg-none mt-3 text-center">
                     <i class="fas fa-user-circle fa-2x dropdown-toggle" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false" style="cursor: pointer;"></i>
                     <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                           <a class="dropdown-item" href="./page-login.php">
                              <i class="fas fa-sign-in-alt me-2"></i>Login
                           </a>
                        </li>
                        <?php /*
                        <li>
                           <a class="dropdown-item" href="./login_register_page.html#register">
                              <i class="fas fa-user-plus me-2"></i>Register
                           </a>
                        </li>
                        */ ?>
                     </ul>
                  </div>
               </div>
            </div>
            <!-- User Menu -->
            <div class="user__menu-icon dropdown d-none d-lg-block">
               <i class="fas fa-user-circle fa-2x dropdown-toggle" role="button" data-bs-toggle="dropdown"
                  aria-expanded="false" style="cursor: pointer;"></i>
               <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                     <a class="dropdown-item" href="./page-login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                     </a>
                  </li>
                  <?php /*
                  <li>
                     <a class="dropdown-item" href="./login_register_page.html">
                        <i class="fas fa-user-plus me-2"></i>Register
                     </a>
                  </li>
                  */ ?>
               </ul>
            </div>
         </div>
      </nav>
   </header>
   <!-- End Header Navigation -->
   <main>