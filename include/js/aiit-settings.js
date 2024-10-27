function openTab(evt, tabName) {
    // Hide all elements with class="tab-content" by default
    var i, tabcontent;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Remove the "nav-tab-active" class from all elements with class="nav-tab"
    var tablinks = document.getElementsByClassName("nav-tab");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
    }

    // Show the specific tab content and add an "nav-tab-active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " nav-tab-active";
    history.pushState(null, null, '#' + tabName);
}

// Function to open a tab based on the URL hash
function openTabFromHash(hash) {
    var tabName = hash.replace('#', '');
    var tab = document.getElementById(tabName);
    if (tab) {
        // Hide all tab content and remove "nav-tab-active" class from all tab buttons
        var tabContents = document.getElementsByClassName("tab-content");
        var tabButtons = document.getElementsByClassName("nav-tab");
        
        for (var i = 0; i < tabContents.length; i++) {
            tabContents[i].style.display = "none";
            tabButtons[i].classList.remove("nav-tab-active");
        }

        // Show the specific tab content and add the "nav-tab-active" class to the button
        tab.style.display = "block";
        var tabButton = document.querySelector('.nav-tab[onclick*="' + tabName + '"]');
        if (tabButton) {
            tabButton.classList.add("nav-tab-active");
        }
    }
}

// Listen for hash changes
window.addEventListener('hashchange', function() {
    openTabFromHash(window.location.hash);
});

// Check the hash when the page loads
document.addEventListener('DOMContentLoaded', function() {
    openTabFromHash(window.location.hash);
});