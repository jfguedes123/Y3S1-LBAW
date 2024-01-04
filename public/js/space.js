var ownerChannel;
var userChannel;
var currentOwner;

function setupOwnerChannel(owner) {
    ownerChannel = pusher.subscribe('private-lbaw2372.' + owner);

    ownerChannel.bind('pusher:subscription_succeeded', function () {
        console.log('Owner Channel - Subscription succeeded');
    });

    ownerChannel.bind('notification-spaceLike', function (data) {
        console.log('Owner Channel - Space ID:', data.space_id);
        console.log('Owner Channel - Message:', data.message);
        data.message = 'Someone Liked your space.';
        showNotification(data.message);
    });
}

function setupUserChannel(user) {
    userChannel = pusher.subscribe('private-lbaw2372.' + user);

    userChannel.bind('pusher:subscription_succeeded', function () {
        console.log('User Channel - Subscription succeeded');
    });

    userChannel.bind('notification-spaceLike', function (data) {
        console.log('User Channel - Space ID:', data.space_id);
        console.log('User Channel - Message:', data.message);
        showNotification(data.message);
    });
}

function useCurrentOwner() {
  console.log('The value of the currentOwner OUTSIDE is', currentOwner);
}

let currentUrl = window.location.href;
let currentUrlParts = currentUrl.split('/');
let currentUrlOwner = currentUrlParts[currentUrlParts.length - 1];
setupOwnerChannel(window.spaceUserId);


function changeLikeState(id, liked, user, owner) {
    let url, data3;
    currentOwner = owner;
    useCurrentOwner();

    console.log("The value of the currentOwner is",currentOwner);
    let countElement = document.getElementById('countSpaceLikes' + id);
    let currentCount = parseInt(countElement.textContent);
    let likeButton = document.getElementById('likeButton' + id);
    console.log('The value of the currentOwner INSIDE is', currentOwner);
    if (!userChannel || !userChannel.subscribed) {
        // Set up the user channel if it's not already set up
        setupUserChannel(user);
    }

    switch (liked) {
        case true:
            url = '/space/unlike';
            data3 = { id: id };
            sendAjaxRequest('DELETE', url, data3, function (response) {
                console.log('Response:', response);
                countElement.textContent = currentCount - 1;
                likeButton.setAttribute('onclick', `changeLikeState(${id}, false,${user},${owner})`);
            });
            break;
        case false:
            let url2 = '/space/like';
            let data2 = { id: id };
            sendAjaxRequest('POST', url2, data2, function (response) {
              if (this.status == 200) {
                console.log('Response:', response);
                countElement.textContent = currentCount + 1;
                likeButton.setAttribute('onclick', `changeLikeState(${id}, true,${user},${owner})`);
                
                // Notify the user when they like a space
                userChannel.trigger('client-notification-spaceLike', {
                    space_id: id,
                    message: 'You liked the space.'
                });}
                else
                {
                  console.log('Response:', response);
                  showNotificationC("You cant like spaces from private users");
                }
            });
            break;
    }
}


function deleteSpace(id) {
    if (!confirm('Are you sure you want to delete this space?')) {
      return;
    }
    
    var url = `/api/space/${id}`;
    var method = 'DELETE';
    var data = null; // No data to send for a DELETE request
    
    sendAjaxRequest(method, url, data, function(event) {
      if (event.target.status === 200) {
        var response = JSON.parse(event.target.responseText);
        console.log(response); // Log the server response (optional)
        
        // Redirect to the appropriate URL based on whether the user is an admin
        if (response.isAdmin) {
          window.location.href = '/admin';
        } else {
          window.location.href = '/homepage';
        }
      } else {
        console.error('Error:', event.target.status, event.target.statusText);
      }
    });
    }

    function resetEditState(id) {
        let space = document.querySelector("#space" + id);
        let main = space.querySelector(".spacecontent");
        
        // Hide the cancel button
        document.querySelector('#cancelEditSpace' + id).style.visibility = 'hidden';
        
        // Change the button back to edit
        let edit_button = document.querySelector("#editSpace" + id);
        let edit_button_icon = edit_button.querySelector("#text-icon");
        edit_button_icon.classList.remove("fa-floppy-o");
        edit_button_icon.classList.add("fa-pencil");
        
        // Restore the original onclick function
        let button = document.querySelector('#editSpace' + id);
        button.onclick = function () {
          editSpace(id);
        };
        }

    function editSpace(id) {
        let space = document.querySelector("#space" + id);
        if (!space) {
            console.error("Space element not found");
            return;
        }
      
        let main = space.querySelector(".spacecontent");
      
        if (!main) {
            console.error("Main element not found within the space element");
            return;
        }
      
        // Save the original content for cancel action
        let originalContent = main.textContent.trim();
        main.dataset.originalContent = originalContent; 
        
        // transformar o content numa caixa de texto
        let textarea = document.createElement('textarea');
        textarea.type = 'textbox';
        textarea.className = 'spacecontent';
        textarea.value = originalContent;
        main.innerHTML = ''; // Clear the main content
        main.appendChild(textarea);
      
        // construção de uma checkbox com base no .innerHTML
        document.querySelector('#cancelEditSpace' + id).style.visibility = 'visible';
      
        // change button edit to confirm
        let edit_button = document.querySelector("#editSpace" + id);
        let edit_button_icon = edit_button.querySelector("#text-icon");
        edit_button_icon.classList.remove("fa-pencil");
        edit_button_icon.classList.add("fa-floppy-o");
      
        // mudar o onclick do botão
        let button = document.querySelector('#editSpace' + id);
        button.onclick = function () {
          // Get the updated content and visibility
          let updatedContent = textarea.value;
      
          // Send an AJAX request to update the content on the server
          let url = '/space/' + id // Replace with the actual server endpoint
          let data = {
            id: id,
            content: updatedContent
          };
          console.log('The value of data from space is',data);
          sendAjaxRequest('PUT', url, data, function (response) {
            console.log('Updated Content:', updatedContent);
            // Update the content on the page
            main.innerHTML = updatedContent;
            // Update the originalContent data attribute
            main.dataset.originalContent = updatedContent;
            showNotification('Space updated successfully');
            // Reset the edit state
            resetEditState(id);
          });
        };
      }
      function cancelEditSpace(id) {
      let space = document.querySelector("#space" + id);
      let main = space.querySelector(".spacecontent");
      // Restore the original content
      main.textContent = main.dataset.originalContent;
      // Reset the edit state
      resetEditState(id);
      }