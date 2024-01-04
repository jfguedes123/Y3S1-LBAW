var modal = document.getElementById("myModal");

var btn = document.getElementById("openFormButton");

var span = document.getElementsByClassName("close")[0];

btn.onclick = function() {
  modal.style.display = "block";
  setTimeout(function() {
    modal.style.opacity = "1";
  }, 50); 
}

span.onclick = function() {
  modal.style.opacity = "0";
  setTimeout(function() {
    modal.style.display = "none";
  }, 500); 
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.opacity = "0";
    setTimeout(function() {
      modal.style.display = "none";
    }, 500); 
  }
}


function resetEditGroup(id) {
  let group = document.querySelector("#group" + id);

  document.querySelector('#cancelEditGroup' + id).style.visibility = 'hidden';

  // Change the button back to edit
  let edit_button = document.querySelector("#editGroup" + id);
  document.querySelector("#text-icon" + id).classList.remove("confirm");
  edit_button.textContent = 'EDIT';

  // Restore the original onclick function
  edit_button.onclick = function () {
      editGroup(id);
  };
}

function editGroup(id) {
  let group = document.querySelector("#group" + id);
  let contentContainer = group.querySelector(".groupcontent-card");

  // Save the original content for cancel action
  let originalName = contentContainer.querySelector(".groupname").textContent.trim();
  let originalDescription = contentContainer.querySelector(".groupcontent").textContent.trim();

  // Transform the content into two text boxes
  let nameTextarea = document.createElement('textarea');
  nameTextarea.className = 'groupname';
  nameTextarea.value = originalName;

  let descriptionTextarea = document.createElement('textarea');
  descriptionTextarea.className = 'groupdescription';
  descriptionTextarea.value = originalDescription;

  contentContainer.innerHTML = ''; // Clear the main content
  contentContainer.appendChild(nameTextarea);
  contentContainer.appendChild(descriptionTextarea);

  // Show the cancel button
  document.querySelector('#cancelEditGroup' + id).style.visibility = 'visible';

  // Change the button to confirm
  let edit_button = document.querySelector("#editGroup" + id);
  edit_button.textContent = 'Confirm';

  contentContainer.dataset.originalName = originalName;
  contentContainer.dataset.originalDescription = originalDescription;

  // Change the onclick of the button
  edit_button.onclick = function () {
      // Get the updated content and visibility
      let updatedName = nameTextarea.value;
      let updatedDescription = descriptionTextarea.value;

      // Send an AJAX request to update the content on the server
      let url = '/group/edit';
      let data = {
          id: id,
          name: updatedName,
          description: updatedDescription
      };

      sendAjaxRequest('PUT', url, data, function (response) {
          console.log('Updated Content:', updatedName, updatedDescription);

          // Create new divs for the name and description
          let newNameDiv = document.createElement('div');
          newNameDiv.className = 'groupname';
          newNameDiv.textContent = updatedName;

          let newDescriptionDiv = document.createElement('div');
          newDescriptionDiv.className = 'groupcontent';
          newDescriptionDiv.textContent = updatedDescription;

          // Replace the textareas with the new divs
          contentContainer.innerHTML = '';
          contentContainer.appendChild(newNameDiv);
          contentContainer.appendChild(newDescriptionDiv);

          // Hide the cancel button
          document.querySelector('#cancelEditGroup' + id).style.visibility = 'hidden';

          // Change the button back to edit
          let edit_button = document.querySelector("#editGroup" + id);
          edit_button.textContent = 'Edit';
          showNotification('Group updated successfully');
          // Restore the original onclick function
          edit_button.onclick = function () {
              editGroup(id);
          };
      });
  };
}

function cancelEditGroup(id) {
  let group = document.querySelector("#group" + id);
  let contentContainer = group.querySelector(".groupcontent-card");

  // Get the original content from the content container's dataset
  let originalName = contentContainer.dataset.originalName;
  let originalDescription = contentContainer.dataset.originalDescription;

  // Create new divs for the name and description
  let newNameDiv = document.createElement('div');
  newNameDiv.className = 'groupname';
  newNameDiv.textContent = originalName;

  let newDescriptionDiv = document.createElement('div');
  newDescriptionDiv.className = 'groupcontent';
  newDescriptionDiv.textContent = originalDescription;

  // Replace the textareas with the new divs
  contentContainer.innerHTML = '';
  contentContainer.appendChild(newNameDiv);
  contentContainer.appendChild(newDescriptionDiv);

  // Reset the edit state
  resetEditGroup(id);
}

function deleteGroup(id) {
  console.log('The value of the id is', id);
  if (!confirm('Are you sure you want to delete this group?')) {
      return;
  }

  var url = `/api/group/${id}`;
  var method = 'DELETE';

  sendAjaxRequest(method, url, null, function(response) {
      if (this.status == 200) {
          console.log(response); // Log the server response (optional)
          if (response.isAdmin) {
              window.location.href = '/admin';
          } else {
              window.location.href = '/homepage';
          }
      }
  });
}


function changeGroupState(id,user_id,publicGroup)
{
  console.log('The boolean of the public group is',publicGroup);
  const state_in_html = document.querySelector('#groupState' + id).innerHTML.replace(/\s/g, '');
  const state = state_in_html.replace( /(<([^>]+)>)/ig,'');
  const button = document.querySelector('#groupState' + id);
  const status = button.dataset.status;

  switch(state) {
    case 'JoinGroup':
      if(publicGroup == null) {
        console.log('entered in a public group');
        let url = '/group/join';
        let data = {
          id: id,
          user_id: user_id
        };
        // Send the AJAX request
        sendAjaxRequest('POST', url, data, function(response) {
          if(this.status == 200){
            button.textContent = 'Pending';

          console.log('Response:', response);}

        });
      }
      else 
      {
        console.log('The value of the id is',id);
        console.log('The value of the user_id is',user_id);
        sendAjaxRequest('POST', '/group/joinrequest', {id: id, user_id: user_id}, function(response) {
          if(this.status) {
            button.textContent = 'Pending';

            console.log("the value of the status is",this.status);}
          console.log('Response:', response);
        });
      }
      break;
      case 'LeaveGroup': 
      let url = '/group/leave';
      let data = {
        id: id,
        user_id: user_id
      };
      sendAjaxRequest('DELETE', url, data, function(response){
        if(this.status == 200) {
          button.textContent = 'Join Group';

        console.log("The value of the status is",this.status);
        console.log('Response:', response);
        
      }
      });
  }
}


function isFavorite(id, groupId)
{
  let button = document.querySelector('#fav' + groupId);
  if(button.className == 'group-interaction-button fa fa-star') // corrected class name
  {
    sendAjaxRequest('PUT', '/group/unfavorite', {user_id:id,group_id: groupId,is_favorite: false},function(response) {
    button.className = 'group-interaction-button fa fa-star-o'
  });}
  else
  {
    sendAjaxRequest('PUT', '/group/favorite', {user_id:id,group_id: groupId,is_favorite: true},function(response) {
    button.className = 'group-interaction-button fa fa-star'
  });
}
}


function deleteMember(id) {
  var pathParts = window.location.pathname.split('/');
  var groupId = parseInt(pathParts[pathParts.length - 1]);

  console.log('The value of the id is', id);
  console.log('The value of the groupId is', groupId);
  if (!confirm('Are you sure you want to delete this member?')) {
    return;
  }

  var url = '/api/group/member/' + id;
  var method = 'DELETE';
  
  let data = {
    groupId: groupId,
    userId: id
  };

  sendAjaxRequest(method, url, data, function(response) {
    if (this.status == 200) {
      showNotification('Member deleted successfully');
      console.log("The value of the status is",this.status);
      console.log(response); // Log the server response (optional)

      // Get the member element with the correct id
      let memberElement = document.querySelector('#member-' + id);

      // Remove the member element from the HTML
      memberElement.remove();
    }
  });
}