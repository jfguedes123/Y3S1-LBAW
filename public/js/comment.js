function resetEditStateComment(id) {
    let comment = document.querySelector("#comment" + id);
    let content = comment.querySelector(".content");
    console.log(content);
  
    // Restore the original content
    content.textContent = content.dataset.originalContent;
  
    // Hide the cancel button
    document.querySelector('#cancelEditComment' + id).style.visibility = 'hidden';
  
    // Change the button back to edit
    let edit_button = document.querySelector("#editComment" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-floppy-o");
    edit_button_icon.classList.add("fa-pencil");
  
    // Restore the original onclick function
    let button = document.querySelector('#editComment' + id);
    button.onclick = function () {
        editComment(id);
    };
  }
  
  function cancelEditComment(id) {
    let comment = document.querySelector("#comment" + id);
    let content = comment.querySelector(".content");
    // Restore the original content
    content.textContent = content.dataset.originalContent;
    // Reset the edit state
    resetEditStateComment(id);
  }
  
  
  function editComment(id) {
    let comment = document.querySelector("#comment" + id);
    console.log(id);
    console.log(comment);
  
    if (!comment) {
        console.error("Comment element not found");
        return;
    }
  
    let content = comment.querySelector(".content");
  
    if (!content) {
        console.error("Content element not found within the comment element");
        return;
    }
  
    // Save the original content for cancel action
    let originalContent = content.textContent.trim();
    content.dataset.originalContent = originalContent;
  
    // Transform the content into a textbox
    let textarea = document.createElement('textarea');
    textarea.type = 'textbox';
    textarea.className = 'content';
    textarea.value = originalContent;
    content.innerHTML = ''; // Clear the content
    content.appendChild(textarea);
  
    // Make the cancel button visible
    document.querySelector('#cancelEditComment' + id).style.visibility = 'visible';
  
    // Change the edit button to a confirm button
    let edit_button = document.querySelector("#editComment" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-pencil");
    edit_button_icon.classList.add("fa-floppy-o");
  
    // Change the onclick of the button
    let button = document.querySelector('#editComment' + id);
    button.onclick = function () {
      // Get the updated content
      let updatedContent = textarea.value;
      // Update the content on the page
      content.innerHTML = updatedContent;
  
      // Send an AJAX request to update the content on the server
      let url = '/comment/edit'; // Replace with the actual server endpoint
      let data = {
        id: id,
        content: updatedContent
      };
      console.log('The value of data is',data);
  
  
      sendAjaxRequest('PUT', url, data, function (response) {
        showNotification('Comment updated successfully')
        content.innerHTML = updatedContent;
        content.dataset.originalContent = updatedContent;
        console.log('Updated Content:', updatedContent);
        resetEditStateComment(id);
      });
    };
  
  }

  function changeLikeStateC(id, liked, user, owner) {
    let url, data;
    let countElement = document.getElementById('countCommentLikes' + id);
    let currentCount = parseInt(countElement.textContent);
    let likeButton = document.getElementById('likeButton' + id);
  
    switch (liked) {
      case true:
        url = '/comment/unlike';
        data = { id: id };
        sendAjaxRequest('DELETE', url, data, function (response) {
            console.log('Response:', response);
            countElement.textContent = currentCount - 1;
            likeButton.setAttribute('onclick', `changeLikeStateC(${id}, false,${user},${owner})`);
  
        });
        break;
      case false:
        url = '/comment/like';
        data = { id: id };
        sendAjaxRequest('POST', url, data, function (response) {
          if(this.status == 200){
            console.log("the value of the status is",this.status);
            console.log('Response:', response);
            countElement.textContent = currentCount + 1;
            likeButton.setAttribute('onclick', `changeLikeStateC(${id}, true,${user},${owner})`);
          }
          else 
          {
            showNotificationC('You cant like comments from private users');
          }
        });
        break;
    }
  }


  function deleteComment(id) {
    if (!confirm('Are you sure you want to delete this comment?')) {
        return;
    }
  
    var pathParts = window.location.pathname.split('/');
    var spaceId = pathParts[pathParts.length - 1];
    var url = `/api/comment/${id}`;
    var method = 'DELETE';
    var data = null; // No data to send for a DELETE request
  
  sendAjaxRequest(method, url, data, function(event) {
    if (event.target.status === 200) {
      console.log(event.target.responseText); // Log the server response (optional)
      
      // Redirect to the back URL after successful deletion
      window.location.href = '/space/' + spaceId;
    } else {
      console.error('Error:', event.target.status, event.target.statusText);
    }
  });
  }
  