
function filterUsers() {
  var input, filter, cards, card, h2, i, txtValue;
  input = document.getElementById("searchc");
  filter = input.value.toUpperCase();
  cards = document.getElementsByClassName("profile-card");

  for (i = 0; i < cards.length; i++) {
      card = cards[i];
      h2 = card.getElementsByTagName("h2")[0];
      txtValue = h2.textContent || h2.innerText;

      if (txtValue.toUpperCase().indexOf(filter) > -1) {
          card.style.display = "";
      } else {
          card.style.display = "none";
      }
  }
}


// Get the modal
var modal = document.getElementById("editUserModal");
console.log(modal);

// Get the button that opens the modal
var btn = document.getElementById("openEditUserModal");

console.log(btn);

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
  setTimeout(function() {
    modal.style.opacity = "1";
  }, 50); // Delay to ensure the modal is visible before starting the transition
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.opacity = "0";
  setTimeout(function() {
    modal.style.display = "none";
  }, 500); // Delay to ensure the transition finishes before hiding the modal
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.opacity = "0";
    setTimeout(function() {
      modal.style.display = "none";
    }, 500); // Delay to ensure the transition finishes before hiding the modal
  }
}



function resetEditUserState(id) {
    let user = document.querySelector("#user" + id);
    let name = user.querySelector(".name");
    let email = user.querySelector(".email");
  
    // Hide the cancel button
    document.querySelector('#cancelEditUser' + id).style.visibility = 'hidden';
  
    // Change the button back to edit
    let edit_button = document.querySelector("#editUser" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-floppy-o");
    edit_button_icon.classList.add("fa-pencil");
  
    // Restore the original onclick function
    let button = document.querySelector('#editUser' + id);
    button.onclick = function () {
        editUser(id);
    };
  }
  
  function editUser(id,publicProfile) {
    console.log(publicProfile);
    let user = document.querySelector("#user" + id);
    console.log(user)
    if (!user) {
        console.error("User element not found");
        return;
    }

    let selectElement = user.querySelector('#publicProfileSelect');
    if (!selectElement) {
        selectElement = document.createElement('select');
        selectElement.id = 'publicProfileSelect';

        let publicOption = document.createElement('option');
        publicOption.value = 'Public';
        publicOption.text = 'Public';

        let privateOption = document.createElement('option');
        privateOption.value = 'Private';
        privateOption.text = 'Private';

        selectElement.appendChild(publicOption);
        selectElement.appendChild(privateOption);
        user.appendChild(selectElement);
    }

    selectElement.value = publicProfile;
    selectElement.style.display = 'block';
  
    let name = document.querySelector(".name");
    let email = document.querySelector(".email");
    console.log("The value of the name is",name);
    console.log("The value of the email is",email);
  
  
    if (!name || !email) {
        console.error("Name or Email element not found within the user element");
        return;
    }
  
    // Save the original content for cancel action
    let originalName = name.textContent.trim();
    let originalEmail = email.textContent.trim();
    console.log("The value of the originalName is",originalName);
    console.log("The value of the originalEmail is",originalEmail);
    name.dataset.originalContent = originalName;
    email.dataset.originalContent = originalEmail;
  
    // transform the content into a text box
    let nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.className = 'name';
    nameInput.value = originalName;
    name.innerHTML = ''; // Clear the name content
    name.appendChild(nameInput);
  
    let emailInput = document.createElement('input');
    emailInput.type = 'email';
    emailInput.className = 'useremail';
    emailInput.value = originalEmail;
    email.innerHTML = ''; // Clear the email content
    email.appendChild(emailInput);
  
    // make the cancel button visible
    document.querySelector('#cancelEditUser' + id).style.visibility = 'visible';
  
    // change button edit to confirm
    let edit_button = document.querySelector("#editUser" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-pencil");
    edit_button_icon.classList.add("fa-floppy-o");
  
    // change the onclick of the button
    let button = document.querySelector('#editUser' + id);
    console.log("The value of the select element is",selectElement.value);

    button.onclick = function () {
        // Get the updated name and email
        let updatedName = nameInput.value;
        let updatedEmail = emailInput.value;
        
        if(selectElement.value == 'Private') {
          privacy = true;
      } else if(selectElement.value == 'Public') {
          privacy = false;
      } else {
          // If none of the options are selected, don't change the privacy
          privacy = null;
      }
  
        // Send an AJAX request to update the name and email on the server
        let url = '/profile/edit'
        if(privacy != null) 
        {
          var data = {
            id: id,
            name: updatedName,
            email: updatedEmail,
            is_public : privacy
        };
        }
        else 
        {
          var data = {
            id: id,
            name: updatedName,
            email: updatedEmail,
        };
        }
        sendAjaxRequest('POST', url, data, function (response) {
          console.log(this.status);
          console.log("the value of the respoinse is",privacy);
            console.log('Updated Name:', updatedName);
            console.log('Updated Email:', updatedEmail);
            // Update the name and email on the page
            name.innerHTML = updatedName;
            email.innerHTML = updatedEmail;
            // Update the originalContent data attribute
            name.dataset.originalContent = updatedName;
            email.dataset.originalContent = updatedEmail;
            selectElement.style.display = 'none';
            showNotification('Profile updated successfully');
            // Reset the edit state
            resetEditUserState(id);

        });
    };
  }
  
  function cancelEditUser(id) {
    let user = document.querySelector("#user" + id);
    let name = user.querySelector(".name");
    let email = user.querySelector(".email");
    let selectElement = user.querySelector("#publicProfileSelect"); 
    console.log("The value of the select element is",selectElement.value);
    

    // Restore the original content
    name.textContent = name.dataset.originalContent;
    email.textContent = email.dataset.originalContent;
    selectElement.style.display = 'none';


    // Reset the edit state
    resetEditUserState(id);
  }

  
  function deleteProfile(id) {
    if (!confirm('Are you sure you want to delete your account?')) {
        return;
    }
  
    var url = `/api/profile/${id}`;
    var method = 'DELETE';
    var data = null; // No data to send for a DELETE request
  
    sendAjaxRequest(method, url, data, function(response) {
      // Check for the 'logout' flag
      if (response.logout) {
          window.location.href = '/logout';
      } else {
          window.location.href = '/homepage';
      }
    });
  }

  


  function acceptFollowRequest(user_id1,user_id2)
{
  let url = '/profile/followsrequest/'+ user_id2;
  console.log('the value of the url in accept is',url);
sendAjaxRequest('POST', url, {user_id1: user_id1,user_id2:user_id2}, function(response) {

  console.log('Response:', response);
});
}


function changeProfileState(user_id2,user_id1,publicProfile)
{
  console.log('The boolean of the public profile is',publicProfile);
  const state_in_html = document.querySelector('#profileState' + user_id2).innerHTML.replace(/\s/g, '');
  const state = state_in_html.replace( /(<([^>]+)>)/ig,'');
  switch(state) {
    case 'Follow':
      if(publicProfile == null) {
        console.log('entered in a public profile');
        let url = '/profile/follow/' + user_id2;
        let data = {
          user_id1: user_id1,
          user_id2: user_id2
        };
        // Send the AJAX request
        sendAjaxRequest('POST', url, data, function(response) {
          if(this.status == 200){
          document.querySelector('#profileState' + user_id2).innerHTML = '<i id="text-icon" aria-hidden="true"></i> Unfollow';}
        });
      }
      else 
      {
        console.log('The value of the id is',user_id2);
        console.log('The value of the user_id is',user_id1);
        sendAjaxRequest('POST', '/profile/followsrequest', {user_id1: user_id1, user_id2: user_id2}, function(response) {
          console.log('Response:', response);
          // Change the button text to 'Pending'
          document.querySelector('#profileState' + user_id2).innerHTML = 'Pending';
        });
      }
      break; 
      case 'Unfollow':
      let url = '/profile/unfollow/' + user_id2;
      let data = {
        user_id1: user_id1,
        user_id2: user_id2
      };
      sendAjaxRequest('DELETE', url, data, function(response){
        if(this.status == 200)
        {
          document.querySelector('#profileState' + user_id2).innerHTML = '<i id="text-icon" aria-hidden="true"></i> Follow';
        }
      });
    }
} 