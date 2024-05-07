<?php ?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>My Shared Lists - Pricing plans</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel="stylesheet" href="./style_pricing.css">

</head>
<body>
<!-- partial:index.partial.html -->
<!-- 

INSTRUCTIONS:
- Using html and css, create the design we shared with you on Figma.

WHAT WE EXPECT FROM YOU:
- Your code should be scalable and maintainable.
- Your code should be component-based.
- Your code should support all modern browsers.
- You are completely free to use the new css features.
-->

<!-- YOUR HTML STARTS HERE -->

<section class="plans__container">
  <div class="plans">
    <div class="plansHero">
      <h1 class="plansHero__title">My Shared Lists</h1>
      <p class="plansHero__subtitle">No contracts. No suprise fees.</p>
    </div>
    <div class="planItem__container">
      <!--free plan starts -->
      <div class="planItem planItem--free">

        <div class="card">
          <div class="card__header">
            <div class="card__icon symbol symbol--rounded"></div>
            <h2>Free</h2>
          </div>
          <div class="card__desc">-</div>
        </div>

        <div class="price">$0<span>/ month</span></div>

        <ul class="featureList">
          <li>2 Created Lists</li>
          <li>5 Joinable Lists</li>
          <li class="disabled">AI Integration</li>
          <li class="disabled">Priority support</li>
          <li class="disabled">List security with roles</li>
        </ul>

        <button class="button">Get Started</button>
      </div>
      <!--free plan ends -->

      <!--pro plan starts -->
      <div class="planItem planItem--pro">
        <div class="card">
          <div class="card__header">
            <div class="card__icon symbol"></div>
            <h2>Basic</h2>
            <div class="card__label label">Best Value</div>
          </div>
          <div class="card__desc">-</div>
        </div>

        <div class="price">$2<span>/ month</span></div>

        <ul class="featureList">
          <li>5 Creatable Lists</li>
          <li>15 Joinable Lists</li>
          <li>AI Integration</li>
          <li class="disabled">Priority support</li>
          <li class="disabled">List security with roles</li>
        </ul>

        <button class="button button--pink">Get Started</button>
      </div>
      <!--pro plan ends -->

      <!--entp plan starts -->
      <div class="planItem planItem--entp">
        <div class="card">
          <div class="card__header">
            <div class="card__icon"></div>
            <h2>Premium</h2>
          </div>
          <div class="card__desc">-</div>
        </div>

        <div class="price">$5<span>/ month</span></div>

        <ul class="featureList">
          <li>Unlimited Creatable Lists</li>
          <li>Unlimited Joinable Lists</li>
          <li>AI Integration</li>
          <li>Priority support</li>
          <li>List security with roles</li>
          <li>Customizable profile</li>
        </ul>

        <button class="button button--pink">Get Started</button>
      </div>
      <!--entp plan ends -->

    </div>
  </div>
</section>
<!-- partial -->
  
</body>
</html>
