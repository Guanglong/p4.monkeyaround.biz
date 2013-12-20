p4.monkeyaround.biz
===================

p4.monkeyaround.biz for DWA-15 (Harvard University)

Application name: Weight and See

It provides a platform for people to monitor weight loss progress.

At Weight and See,  user can 
1) sign in/sign up
2) Set a goal of weight by certain number of days,
3) Log the weight data, see the nice chart and the prediction based on the data :-(

Pleas note that the following list of features:

1): user's sign-up IP address and the most recent login IP address are recorded
2): email notification upon sign up (if email is deliverable)
3): Sign-in, Sign-Up Via AJAX.
4): Duplicate Email Validation done by both the server-side PHP, and the client side javascript.
5): the menu color is changed for the current page.
6): the display of the average goal (lbs/day), the value is calculated in PHP, not stored in mysql database.
This additional info was added based on the suggestions from the demo


The following aspects are managed by Javascript:
1): Sign up Validation
2): Duplicate email address validation.
3): JQuery: many JQuery usages, include JQuery UI -Dialog, jQuery ajax $.ajax
3): Google Chart API.
4): Google Table API
5): "Google Prediction API":It is just for fun, not for real :-) 
6): The home page images changes
