<main id="content">
  <div class="page-content mt-3 mb-3">
    <div class="container">
      <div class="row">

        <!-- About Me (Left Sidebar) Start -->
        <div class="col-md-4">
          <div>
            <div class="my-pic">
              <img src="<?= "https://ui-avatars.com/api/?name=" . $user->get('display_name') ?>" alt="<?= $user->get('display_name') ?>">
              <div id="menu">
                <ul class="menu-link">
                  <li><a href="<?= ap_route('user_profile', $user->get('user_login')) ?>">About</a></li>
                  <li><a href="<?= site_url(ap_route('user_profile', $user->get('user_login'))) ?>">Work</a></li>
                  <li><a href="contact.html">Contact</a></li>
                </ul>
              </div>
            </div>



            <div class="my-detail">

              <div class="white-spacing">
                <h1><?= $user->get('display_name') ?> <?= $user->get('user_nicename') ? '(' . $user->get('user_nicename') . ')' : '' ?></h1>
                <span><?= $user->get('title') ?></span>
              </div>

              <div id="menu">
                <ul class="menu-link">
                  <li><a href="<?= ap_route('profile.edit') ?>">Edit</a></li>
                  <li><a href="<?= wp_logout_url() ?>">Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- About Me (Left Sidebar) End -->





        <!-- About Me (Right Sidebar) Start -->
        <div class="col-md-8">
          <div class="col-md-12 page-body">
            <div class="row">


              <div class="sub-title">
                <h2>About Me</h2>
                <a href="contact"><span class="dashicons dashicons-email"></span></a>
              </div>


              <div class="col-md-12 content-page">
                <div class="col-md-12 ps-4 pe-4">


                  <!-- My Intro Start -->
                  <div class="post-title">
                    <h1>Hi, I am <span class="main-color">Alex Parker</span></h1>

                    <ul class="knowledge">
                      <li class="bg-color-1">Web Designer</li>
                      <li class="bg-color-4">Web Developer</li>
                      <li class="bg-color-6">Freelancer</li>
                      <li class="bg-color-5">Consultant</li>
                    </ul>

                  </div>
                  <!-- My Intro End -->


                  <p>I am in the website field since 2004 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin at quam at orci commodo hendrerit vitae nec eros. Vestibulum neque est, imperdiet nec tortor nec, tempor semper metus. <b>I am a developer</b>, et accumsan nisi. Duis laoreet pretium ultricies. Curabitur rhoncus auctor nunc congue sodales. Sed posuere nisi ipsum, eget dignissim nunc dapibus eget. Aenean elementum sollicitudin sapien ut sapien fermentum aliquet mollis. Curabitur ac quam orci sodales quam ut tempor. suspendisse, gravida in augue in, interdum <b><a href="work.html" data-toggle="tooltip" data-placement="top" title="Check out my work.">Work</a></b> bibendum dui. Suspendisse sit amet justo sit amet diam fringilla commodo. Praesent ac magna at metus malesuada tincidunt non ac arcu. Nunc gravida eu felis vel elementum. Vestibulum sodales quam ut tempor tempor Donec sollicitudin imperdiet nec tortor nec, tempor semper metus..</p>



                  <!-- Video Start -->
                  <div class="video-box margin-top-40 margin-bottom-80">
                    <div class="video-tutorial">
                      <a class="video-popup" href="https://www.youtube.com/watch?v=O2Bsw3lrhvs" title="My Thought">
                        <img src="images/pic/my-pic.png" alt="">
                      </a>
                    </div>
                    <p>Take a look about my thought on website design.</p>
                  </div>
                  <!-- Video End -->




                  <!-- My Service Start -->
                  <div class="post-title">
                    <h1>My <span class="main-color">Services</span></h1>
                  </div>

                  <div class="list list-style-2 margin-top-30">
                    <ul>
                      <li>Website Design</li>
                      <li>Website Development</li>
                      <li>Wordpress Theme Development</li>
                      <li>Mobile Apps UI/UX Design</li>
                      <li>Online Software Development</li>
                      <li>E-commerce Development</li>
                      <li>UI/UX Consulting</li>
                    </ul>
                  </div>
                  <!-- My Service End -->

                </div>

                <div class="col-md-12 text-center">
                  <a href="contact.html" data-toggle="tooltip" data-placement="top" title="Visit on my contact page for hire me." class="load-more-button">Hire</a>
                </div>

              </div>

            </div>



            <!-- Subscribe Form Start -->
            <div class="col-md-8 col-md-offset-2">
              <form id="mc-form" method="post" action="http://uipasta.us14.list-manage.com/subscribe/post?u=854825d502cdc101233c08a21&amp;id=86e84d44b7">

                <div class="subscribe-form margin-top-20">
                  <input id="mc-email" type="email" placeholder="Email Address" class="text-input">
                  <button class="submit-btn" type="submit">Submit</button>
                </div>
                <p>Subscribe to my weekly newsletter</p>
                <label for="mc-email" class="mc-label"></label>
              </form>

            </div>
            <!-- Subscribe Form End -->


          </div>



          <!-- Footer Start -->
          <div class="col-md-12 page-body margin-top-50 footer">
            <footer>
              <ul class="menu-link">
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="work.html">Work</a></li>
                <li><a href="contact.html">Contact</a></li>
              </ul>

              <p>© Copyright 2016 DevBlog. All rights reserved</p>


              <!-- UiPasta Credit Start -->
              <div class="uipasta-credit">Design By <a href="http://www.uipasta.com" target="_blank">UiPasta</a></div>
              <!-- UiPasta Credit End -->


            </footer>
          </div>
          <!-- Footer End -->


        </div>
        <!-- About Me (Right Sidebar) End -->

      </div>
    </div>
  </div>
</main>