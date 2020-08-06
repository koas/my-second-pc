# My Second PC
A website that emulates a desktop environment, written in vanilla JS in 2006. See it in action at [koas.dev/m2pc](https://koas.dev/m2pc). This was created as a demo for the KWS library.

I release this code only as proof of what JavaScript and CSS were capable of in 2006, and although it still works today using this code for any new development is absolutely discouraged.

## Background
The year is 2006. After many years developing custom web apps (mainly for data management and processing) for my company's customers I realized that the classical _one page per section_ approach was not very efficient.

After observing many customers use our apps I could see a common pattern: more often than not they needed to look at the data of one section to perform an action on another section. Most users solved this by opening another browser window with the app and alt-tabbing between both windows, or moving the second window to a second monitor. But some users just printed or wrote down by hand the data they needed.

That's when I came up with the idea of creating a desktop environment on the browser where each section of the app would be a window and you could have multiple windows visible at the same time. Users were already used to having multiple windows on their desktop so reusing that knowledge for a web app seemed like a good idea.


## The KWS library
KWS stands for Karontek's (my company) Window System, and it's a bunch of vanilla JS files for the environment components: desktop, window, taskbar, data tables, desktop icons, traybar, etc. It would take too long to explain in detail how it works and sadly the code has no comments, but I'll try to give some general info about it.

Apps are XML files (yes, XML was THE language back then) with a `code` tag where the JS code for the app lives. After the `code` tag you can have one or more `window` tags, and inside each `window` the UI for that app is created using the library components (like `tabcontrol` or `tab`) or HTML code (inside the `html_code` tag).

The library after loading an app calls the `main` function (which is inside the `code` tag) and that kickstarts the app. Usually it shows one of the windows, loads some data from the server using AJAX and shows it.

## The apps
The code for most of the apps of _My Second PC_ is in the `apps` folder. All the server side code needed for some of this apps to run has been removed, and not all components are used on the demo apps.

## Closing words
All customers for whom we developed applications using the KWS library were very satisfied with the multi-window system and confirmed that the users were much more efficient in their daily work. We have used this method with equal success in our projects ever since.

This demo features the first styles we used for the library. Over the years, the look was changed to accommodate trends in operating system and icon design. A major rewrite was done a few years later using jQuery and two years ago I rewrote it again using Vue.js, which is the current version we use at Karontek.

Two months ago, and because of the COVID-19 global crisis, sadly one of our customers went out of business. They were the last ones who still had a working application that used this library. Since it's not used anywhere anymore my company authorized me to make the code public.

I hope it can be useful to someone for something, though I doubt it. But I'll settle for showing what could be done with the web technology of the mid- and late 2000s.
