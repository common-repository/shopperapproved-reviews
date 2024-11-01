// Loader
function toggleLoading() {
    let loader = document.getElementById('sa-loader-container');

    if (loader.style.display == 'block') {

        setTimeout(function() {
            loader.style.display = 'none';
        }, 1000); // Hide after 1 second

    } else {
        loader.style.display = 'block';
    }
}

/****************Input switch disable button****************/

// enable or disable submit button when user tries to login
let loginInputs = document.querySelectorAll('.login_input');

loginInputs.forEach(function(element) {
    element.addEventListener('keyup', function(event){
        let siteId = document.getElementById('site_id').value;
        let tokenValue = document.getElementById('api_token').value;
        let submitBtn = document.getElementById('sa_btn_submit');

        if (siteId == "" || tokenValue == 0) {
            submitBtn.setAttribute('disabled', 'disabled');
        } else {
            submitBtn.removeAttribute('disabled');
        }
    });
});

// function to show spinner animation on Login page
function showSpinner() {

    const spinner = document.getElementById('sa_spinner');
    spinner.style.display = 'block';
}

// Thank you survey settings screen
let thankuSurvay = document.getElementById('sa_thanku_survay');
if (thankuSurvay) {
    thankuSurvay.addEventListener('change', function(event) {

        event.preventDefault();
        toggleLoading();

        // Check if the event was triggered by the checkbox with the ID 'sa_survey_status' (on step 3)
        if (event.target && event.target.id === 'sa_survey_status') {

            let submitBtn = document.getElementById('sa_continue_survey');
            let submitValue;

            if (event.target.checked) {
                submitValue = true;
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
                submitValue = false;
            }
        }
    });
}

/*************custom dropdown - currently it is being used to create Survey days dropdown *************/
function createCustomDropdowns() {
    let selectBoxes = document.querySelectorAll('select.sa-custom-select');
    selectBoxes.forEach(function(select, i) {

        if (!select.classList.contains('dropdown')) {

            let node = document.createElement('div');
            node.className = 'dropdown ' + (select.getAttribute('class') || '');
            node.setAttribute('tabindex', 0);
            node.innerHTML = '<span class="current"></span><div class="list"><ul></ul></div>';
            select.parentNode.appendChild(node);

            let dropdown = select.nextElementSibling;
            let selected = select.querySelector('option:checked');
            dropdown.querySelector('.current').innerHTML = selected.textContent;

            let options = select.querySelectorAll('option');
            let optionsHtml = ''
            options.forEach(function(j, o) {

                optionsHtml += '<li class="option ' + (selected.textContent == j.innerText ? 'selected' : '') + '" data-value="' + j.value + '" data-display-text="' + j.innerText + '">' + j.innerText + '</li>';
            });

            dropdown.querySelector('ul').innerHTML = optionsHtml;
        }
    });
}

/*********************Switch Toggle Alerts*******************/
document.addEventListener('DOMContentLoaded', function() {

    createCustomDropdowns(); // used in Survey settings page

    let toggleSwitches = document.querySelectorAll('.switch-input');
    let alertBars = document.querySelectorAll('.sa-alert-custom');

    toggleSwitches.forEach(function(switchBtn, index) {
        switchBtn.addEventListener('change', function() {

            // save Seals & Widgets settings
            toggleLoading();

            let currentAlert = alertBars[index];
            let currentAlertText = currentAlert.querySelector('.sa-alert-text');

            let submitValue;
            let submitOption = this.value;

            if (this.checked) {
                currentAlert.classList.remove('warning');
                currentAlert.classList.add('success');

                if (submitOption == 'sa_rp_status') {
                    currentAlertText.textContent = `turned on`;
                } else {
                    currentAlertText.textContent = `enabled`;
                }

                submitValue = true;

            } else {
                currentAlert.classList.remove('success');
                currentAlert.classList.add('warning');

                if (submitOption == 'sa_rp_status') {
                    currentAlertText.textContent = `turned off`;
                } else if (submitOption == 'sa_rotating_widget_status'){
                    currentAlertText.textContent = `turned off. To disable the Rotating Widget, please remove the Rotating Widget Code from any page you've added it to in your Woo Commerce Site.`;
                }else {
                    currentAlertText.textContent = `disabled`;
                }

                submitValue = false;
            }

            // Use XMLHttpRequest to update option (works for Thank You Page Survey and all 5 Seals & Widgets options
            var request = createRequestObject();

            request.onload = function () {
                if (this.status >= 200 && this.status < 400) {
                    // If successful
                    // option is updated via XMLHttpRequest
                    toggleLoading();

                    currentAlert.style.display = 'flex';

                    if (submitValue == false && response.show_rp_code) {
                        currentAlertText.textContent = `turned off. You will need to manually remove the Review Page Widget Code from your /reviews page. Search for the following Review Page Widget Code`;

                        let notesBlock = document.getElementById('sa-notes-block');
                        notesBlock.remove('d-none');

                        let roNote = document.getElementById('sa-rp-note');
                        roNote.style.display = 'none'; // only hide the note, not js code
                    }

                    setTimeout(function() {

                        currentAlert.style.display = 'none';

                    }, 15000); // Hide alert after 10 seconds

                } else {
                    // If fail
                    alert('Request failed!');
                    toggleLoading();
                }
            };

            request.send('action=update_sa_option&option_key=' + submitOption + '&option_value=' + submitValue);
        });
    });
});

// Event listeners

// Open/close (for Surveys dropdown)
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.dropdown').forEach(el => {
        el.onclick = () => {

            let dropdowns = document.querySelectorAll('.dropdown');
            // Reset all dropdowns
            for(var i = 0; i < dropdowns.length; i++){
                if(el != dropdowns[i]){ // close all dropdowns except current one
                    dropdowns[i].classList.remove('open');
                    dropdowns[i].classList.remove('shift-up');
                }
            }
            el.classList.toggle('open');

            let option = el.querySelector('.selected');

            if (option) {
                if (el.classList.contains('open')) {

                    var windowHeight = window.innerHeight;
                    if (windowHeight < 700) {
                        el.classList.add('shift-up'); // Add a class to shift the dropdown upwards
                    }

                    option.scrollIntoView();
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.dropdown .option').forEach(el => {
        el.onclick = () => {

            if (!el.classList.contains('open')) {

                // first remove 'selected'
                let option = document.querySelector('.selected');

                option.classList.remove('selected');
                option.removeAttribute('tabindex');

                if (option) {
                    option.setAttribute('tabindex', 0);
                }

                // select dropdown option and show its value
                let newOption = event.target;

                newOption.classList.add('selected');
                let text = newOption.textContent;
                let value = newOption.getAttribute('data-value');

                document.querySelector('.dropdown .current').innerText = text;

                let dropdownParent = el.parentNode.parentNode.parentNode.previousElementSibling;

                let selectDropdownDivId = dropdownParent.getAttribute('id');

                if (selectDropdownDivId) {
                    let selectDropdownDiv = document.getElementById(selectDropdownDivId);
                    selectDropdownDiv.value = value;
                }
            }
        }
    });
});

// Close when clicking outside dropdown (for Surveys dropdown)
document.addEventListener('click', (event) => {
    if (event.target.closest('.dropdown') === null) {

        let dropdowns = document.querySelectorAll(".dropdown");
        dropdowns.forEach((element) => {
            element.classList.remove("open");
        });

        if (document.querySelector('.dropdown .option')) {
            document.querySelector('.dropdown .option').removeAttribute('tabindex');
        }
    }
    event.stopPropagation();
});

/**************Modals***************/
let modal = document.querySelector(".sa-modal");
let trigger = document.querySelector(".sa-trigger");
let closeButton = document.querySelector(".sa-close-button");

function toggleModal() {
    const body = document.body;

    modal.classList.toggle("sa-show-modal");
    body.classList.toggle("sa-modal-open"); // Adding/removing the class to the body tag
}

if (trigger) {
    trigger.addEventListener("click", toggleModal);
    closeButton.addEventListener("click", toggleModal);
}

/********************* Progressbar***********************/

function showFeedProgressBar() {

    document.querySelectorAll('.sa-feed').forEach((saFeed) => {
        saFeed.style.display = 'none';
    });

    const feed = document.querySelector('.sa-feed-step2');
    feed.style.display = 'block';

    setTimeout(showProgress, 100);
}

function showProgress() {

    let percent1 = 0;

    let timer1 = setInterval(function () {
        percent1 += 1;
        document.querySelector('.sa-complete-feed .sa-counter').innerText = percent1;
        document.querySelector('.sa-complete-feed .sa-counter-progress').style.width =  percent1 + '%';

        if (percent1 >= 100) {
            clearInterval(timer1);
        }
    }, 20);
}

/**
 * Copies text to clipboard
 *
 * @param containerId
 */
function copyText(containerId)
{
    let el = document.getElementById(containerId);

    // this is to deal with text inside both input fields and code fields
    let text = '';
    if (el.value) {
        text = el.value;
    } else {
        text = el.innerText;
    }

    // copy to clipboard text from code
    let code = document.createElement('input');

    code.value = text;
    document.body.appendChild(code);

    code.select();

    document.execCommand('copy');
    document.body.removeChild(code);

    toggleSnackbar('Copied!');
}

// show message if needed
const toggleSnackbar = (message, duration = 3000) => {
    const snackbar = document.getElementById("snackbar");

    snackbar.innerHTML = message;
    snackbar.classList.add("show");

    setTimeout(function () {

        snackbar.classList.remove("show");
    }, duration);
}

// Seals & Widgets Tabs
let continueBtns = document.querySelectorAll('.seal-countine-btn');
document.addEventListener('DOMContentLoaded', function() {

    let backBtns = document.querySelectorAll('.sa-seal-back');
    let step1 = document.querySelector('.sa-seal-step1');
    let step2 = document.querySelector('.sa-seal-step2');

    if (step1 && step2) {
        for (let i = 0; i < continueBtns.length; i++) {
            continueBtns[i].addEventListener('click', function() {
                step1.classList.remove('active');
                step2.classList.add('active');
            });
        }

        for (let j = 0; j < backBtns.length; j++) {
            backBtns[j].addEventListener('click', function() {
                step2.classList.remove('active');
                step1.classList.add('active');
            });
        }
    }
});

/*********************Seals Widgets******************/

function handleToggleSwitch(toggleSwitchId, targetElementsClass) {
    const toggleSwitch = document.getElementById(toggleSwitchId);
    const elements = document.querySelectorAll('.' + targetElementsClass);

    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', function() {
            elements.forEach(function(element) {
                if (toggleSwitch.checked) {
                    element.classList.remove('d-none');

                } else {
                    element.classList.add('d-none');
                }
            });
        });
    }
}

// show/hide additional options under Floating Seal, Review Page and Rotating Page switches
handleToggleSwitch('Seal1', 'sa-floatSeal-notes');
handleToggleSwitch('Seal2', 'sa-reviewPage-notes');
handleToggleSwitch('Seal3', 'sa-rotatingPage-notes');

// Get references to the elements
const continueButton = document.getElementById('sa_continueButton');
const inputValue = document.getElementById('sa_sealurl_value');
const addSealUrl = document.querySelectorAll('.sa-add-seal-url'); // assuming sa-add-seal-url is the ID of the element to show/hide
let installSealButton = document.getElementById('sa_installSeal');

// Function to activate continue button and show sa-add-seal-url
function activateContinue() {
    continueButton.removeAttribute('disabled');
    installSealButton.removeAttribute('disabled');
    addSealUrl.forEach(element => {
        element.classList.remove('visibilty-none');
    });
}

// Function to deactivate continue button and hide sa-add-seal-url
function deactivateContinue() {
    continueButton.setAttribute('disabled', 'true');
    installSealButton.setAttribute('disabled', 'true');
    addSealUrl.forEach(element => {
        element.classList.add('visibilty-none');
    });
}

// Used to add Excluded URL's under Install the Floating Seal
document.addEventListener('DOMContentLoaded', () => {
    const addButton = document.getElementById('sa_addButton');
    const itemList = document.getElementById('sa_sealitemList');
    const inputField = document.getElementById('sa_sealUrl');

    const addSealUrlElements = document.querySelectorAll('.sa-add-seal-url');

    // Function to check and toggle the visibility class based on the presence of items
    function toggleAddSealUrlVisibility() {
        const items = itemList.getElementsByTagName('li');
        const isEmpty = items.length === 0;

        installSealButton.disabled = isEmpty;

        addSealUrlElements.forEach((element) => {
            if (isEmpty) {
                element.classList.add('visibilty-none'); // Hide the add-seal-url

                deactivateContinue();
            } else {
                element.classList.remove('visibilty-none'); // Show the add-seal-url
                activateContinue();

                // enable first Continue button if Seal Excluded URL is added
                continueBtns[0].click();
            }
        });
    }

    if (addButton && itemList) {

        // Event listener for adding new items
        addButton.addEventListener('click', () => {
            if (inputField && inputField.value !== '') {
                const newItem = document.createElement('li');
                const inputValue = document.createElement('span');
                const removeLink = document.createElement('a');

                inputValue.textContent = inputField.value;
                removeLink.textContent = '[ - ]';
                removeLink.classList.add('sa-remove-SealUrl');

                toggleLoading();

                // Use XMLHttpRequest to save excluded seal URL
                var request = createRequestObject();

                request.onload = function () {
                    if (this.status >= 200 && this.status < 400) {
                        // If successful
                        // option is updated via XMLHttpRequest
                        toggleLoading();

                        newItem.appendChild(removeLink);
                        newItem.appendChild(inputValue);
                        itemList.appendChild(newItem);

                        inputField.value = ''; // Clear input field after adding

                        activateContinue(); // Activate the continue button and show sa-add-seal-url elements
                        toggleAddSealUrlVisibility(); // Check the itemList after adding an item

                    } else {
                        // If fail
                        alert('Request failed!');
                        toggleLoading();
                    }
                };

                request.send('action=update_sa_seal_excluded&option_value=' + inputField.value + '&add_seal=' + true);
            }
        });

        // Event listener for removing items
        itemList.addEventListener('click', (event) => {
            if (event.target.classList.contains('sa-remove-SealUrl')) {

                const listItem = event.target.closest('li');
                if (listItem) {

                    toggleLoading();

                    // Use XMLHttpRequest to remove excluded seal URL
                    var request = createRequestObject();

                    request.onload = function () {
                        if (this.status >= 200 && this.status < 400) {
                            // If successful
                            // option is updated via XMLHttpRequest
                            toggleLoading();

                            itemList.removeChild(listItem);
                            toggleAddSealUrlVisibility(); // Check the itemList after removing an item

                        } else {
                            // If fail
                            alert('Request failed!');
                            toggleLoading();
                        }
                    };

                    request.send('action=update_sa_seal_excluded&option_value=' + listItem.childNodes[1].textContent + '&add_seal=' + false);
                }
            }
        });

        // Initially check the item list on page load
        toggleAddSealUrlVisibility();
    }
});

// Install Seal button functionality

if (installSealButton) {
    installSealButton.addEventListener('click', function() {

        let sealBar = document.getElementById('sa_sealbar_floating');
        sealBar.style.display = 'flex'; // Display the seal bar

        toggleLoading();

        // Use XMLHttpRequest to install Seal
        var request = createRequestObject();

        request.onload = function () {
            if (this.status >= 200 && this.status < 400) {
                // If successful
                // option is updated via XMLHttpRequest
                toggleLoading();

                // Remove the seal bar after 5 seconds
                setTimeout(function() {
                    sealBar.style.display = 'none';
                }, 5000); // 5000 milliseconds = 5 seconds

            } else {
                // If fail
                alert('Request failed!');
                toggleLoading();
            }
        };

        request.send('action=update_sa_option&option_key=sa_seal_status2&option_value=' + 1);
    });
}

// show/hide Congratulations screen
let congratsContinue = document.getElementById('congrats-continue');

if (congratsContinue) {

    congratsContinue.addEventListener('click', function() {

        toggleLoading();

        // step 6 is completed successfully

        // Use XMLHttpRequest to update option
        var request = createRequestObject();

        request.onload = function () {
            if (this.status >= 200 && this.status < 400) {
                // If successful
                toggleLoading();

                let step5 = document.getElementById('step-5');
                step5.style.display = 'none';

                let step6 = document.getElementById('step-6');
                step6.style.display = 'block';

                setTimeout(function() {

                    hideCongrats();

                }, 5000); // Hide note after 5 seconds

            } else {
                // If fail
                alert('Request failed!');
                toggleLoading();
            }
        };
        request.send('action=update_sa_option&option_key=sa_step6_status&option_value=' + 1);
    });
}

function hideCongrats() {
    let congrats = document.getElementById('sa-congrats-note');
    congrats.style.display = 'none';
}

// function to create XMLHttpRequest object for ajax requests
function createRequestObject() {
    let request = new XMLHttpRequest();

    request.open('POST', shopperapproved_admin.adminAjax, true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');

    request.onerror = function() {
        // Connection error
        alert('Something went wrong!');
        toggleLoading();
    };

    return request;
}

/* New Product Feed and GTIN changes start */

// "No" step button
function showNoStep() {
    document.querySelector('.sa-feed-step1').style.display = 'none';
    document.querySelector('.sa-feed-step5').style.display = 'block';
}

// "Yes" step button
function showYesStep() {
    document.querySelector('.sa-feed-step1a').style.display = 'none';
    document.querySelector('.sa-feed-step2a').style.display = 'block';
}

// previous button in "Yes/No" step
function stepBack() {
    document.querySelector('.sa-feed-step1').style.display = 'block';
    document.querySelector('.sa-feed-step5').style.display = 'none';

    document.querySelector('.sa-feed-step1a').style.display = 'block';
    document.querySelector('.sa-feed-step2a').style.display = 'none';
}

// Enable Product feed button on set GTIN Attribute button
document.addEventListener('DOMContentLoaded', function () {
    // Get references to the buttons
    var gtinButton = document.getElementById('gtin_button');
    var generateFeedBtn = document.getElementById('generate_feed');

    if (gtinButton) {

        // Add click event listener to the "Set GTIN Attribute" button
        gtinButton.addEventListener('click', function () {

            // save gtin option
            toggleLoading();

            // Use XMLHttpRequest to update option
            var request = createRequestObject();

            request.onload = function () {
                if (this.status >= 200 && this.status < 400) {
                    // If successful
                    toggleLoading();

                    // Enable the "Generate Product Feed" button
                    generateFeedBtn.disabled = false;

                } else {
                    // If fail
                    alert('Request failed!');
                    toggleLoading();
                }
            };

            let gtin = document.getElementById('sa_feed_gtin_attribute');
            request.send('action=update_sa_option&option_key=sa_feed_gtin&option_value=' + gtin.value + '&not_boolean=1');
        });
    }
});

/* New Product Feed and GTIN changes end */