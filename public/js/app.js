function encodeForAjax(data) {
  console.log('In the encode for Ajax the space is',data);
  if (data == null) return null;
  return Object.keys(data).map(function(k){
    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
  }).join('&');
}

function sendAjaxRequest(method, url, data, handler) {
  let request = new XMLHttpRequest();
  console.log('The value of the data is',data);
  request.open(method, url, true);
  request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
  request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  request.addEventListener('load', handler);
  request.send(encodeForAjax(data));
}

function showNotifications(id) {
  var notificationsContainer = document.getElementById('notificationsContainer');
  
  // If notifications are already shown, hide them and return
  if (notificationsContainer.style.display === 'block') {
    notificationsContainer.style.display = 'none';
    return;
  }

  sendAjaxRequest('GET', '/notification', null, function(response) {
    var notifications = JSON.parse(response.target.responseText);
    notificationsContainer.innerHTML = '';
    notifications.forEach(notification => {
      var card = document.createElement('div');
      card.id = 'notification_' + notification[4]; // Set a unique ID for each notification
      card.style.border = '1px solid #ccc';
      card.style.borderRadius = '4px';
      card.style.marginBottom = '10px';
      card.style.padding = '10px';

      var a = document.createElement('a');
      a.href = notification[3];
      
      var notificationType = notification[0].replace('_', ' ');
      console.log("The value of the nofiticationType is",notificationType);
      var userName = notification[1].name;
      var who = notification[2];

      if (notificationType == 'invite') {
        console.log("the value of the notification is",notification);
        var url = notification[3];
        var parts = url.split('/'); // ["", "group", "2"]
        var groupId = parts[2]; // "2"
        console.log(groupId);
        console.log("the value of the url is",url);
        a.textContent = `${userName} has invited you`;
      
        var acceptButton = document.createElement('button');
        acceptButton.textContent = 'Accept';
        acceptButton.style.backgroundColor = 'black';
        acceptButton.style.borderColor = '#1c2632';
        acceptButton.style.marginBottom = '2em';
        acceptButton.addEventListener('click', function() {
          acceptInvite(groupId, notification[4]);
          updateNotification(notification[4]);
        });
      
        var declineButton = document.createElement('button');
        declineButton.textContent = 'Decline';
        declineButton.style.backgroundColor = 'black';
        declineButton.style.borderColor = '#1c2632';
        declineButton.style.marginBottom = '2em';
        declineButton.addEventListener('click', function() {
          updateNotification(notification[4]);
          declineInvite(groupId,notification[4]);
        });
      
        // Append the text and buttons to the card element
        card.appendChild(a);
        card.appendChild(acceptButton);
        card.appendChild(declineButton);
      }
      else if(notificationType == 'request follow') {
        console.log("the value of the notification is",notification);
        var url = notification[3];
        var parts = url.split('/');
        var userid = parts[2];
        console.log(groupId);
        console.log("the value of the url is",url);
        a.textContent = `${userName} has requested to follow you`;
      
        var acceptButton = document.createElement('button');
        acceptButton.textContent = 'Accept';
        acceptButton.style.backgroundColor = 'black';
        acceptButton.style.borderColor = '#1c2632';
        acceptButton.style.marginBottom = '2em';
        acceptButton.addEventListener('click', function() {
          acceptFollowRequest(userid,id);
          updateNotification(notification[4]);

        });
      
        var declineButton = document.createElement('button');
        declineButton.textContent = 'Decline';
        declineButton.style.backgroundColor = 'black';
        declineButton.style.borderColor = '#1c2632';
        declineButton.style.marginBottom = '2em';
        declineButton.addEventListener('click', function() {
          declineFollowRequest(userid,id);
          updateNotification(notification[4]);

        });
      
        // Append the text and buttons to the card element
        card.appendChild(a);
        card.appendChild(acceptButton);
        card.appendChild(declineButton);
      }
      else if(notificationType == 'comment_tagging') {
        var tempDiv = document.createElement("div");
        tempDiv.innerHTML = who;
        var plainTextWho = tempDiv.textContent || tempDiv.innerText || "";
      
        a.textContent = `${userName} ${notificationType} ${plainTextWho}`;
      
        var button = document.createElement('button');
        button.textContent = '✓';
        button.style.backgroundColor = 'black';
        button.style.borderColor = '#1c2632';
        button.style.marginBottom = '2em';
        button.addEventListener('click', function() {
          updateNotification(notification[4]); // Pass the ID to updateNotification
        });
      
        card.appendChild(a);
        card.appendChild(button);
      }
      else {
        console.log("the value of who is",who);
        a.textContent = `${userName} ${notificationType} ${who}`;
        var button = document.createElement('button');
        button.textContent = '✓';
        button.style.backgroundColor = 'black';
        button.style.borderColor = '#1c2632';
        button.style.marginBottom = '2em';
        button.addEventListener('click', function() {
          updateNotification(notification[4]); // Pass the ID to updateNotification
        });

        card.appendChild(a);
        card.appendChild(button);
      }

      notificationsContainer.appendChild(card);
    });
    notificationsContainer.style.display = 'block';
  });
}

function updateNotification(id) {
  console.log(id);
  var url = '/notification/' + id;
  console.log("The url is",url);
  var method = 'PUT';
  console.log("The value of the id is",id);
  var data = {
    id: id
  };

  // Remove the HTML element associated with the notification
  sendAjaxRequest(method, url, data, function(response) {
    if (this.status == 200) {
      console.log(response); // Log the server response (optional)
      var notificationElement = document.getElementById('notification_' + id);
      console.log("THe value of the notificationElement is",notificationElement);
      if (notificationElement) {
        notificationElement.remove();
      }
    }
  });
}

function deleteNotification(id) {
  if (!confirm('Are you sure you want to delete this notification?')) {
    return;
  }
  
  var url = `api/notification/${id}`;
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




let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const pusher = new Pusher('e0f29d0e833b76381d01', {
    cluster: 'eu',
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    }
});

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: "e0f29d0e833b76381d01",
    cluster: "eu",
    encrypted: true
});


function stopFollowing(following)
{
  console.log("The value of the following is", following);
  sendAjaxRequest('DELETE', `/profile/unfollow/${following}`, null, function(response) {
    console.log('Response:', response);
    var profileCard = document.getElementById('profile-card' + following);
      console.log("the value of the card is", profileCard);
      showNotification('You are now not following this user');
      if (profileCard) {
          profileCard.remove();
      }
  });
}


function removeFollower(follower, me) {
  sendAjaxRequest('DELETE', `/profile/unfollow/${me}`, { id: follower }, function (response) {
      console.log("The value of the following is", follower);

      // Find and remove the corresponding profile-card div
      var profileCard = document.getElementById('profile-card' + follower);
      console.log("the value of the card is", profileCard);
      showNotification('This user is now not following you');
      if (profileCard) {
          profileCard.remove();
      }
  });
}



function showNotification(message) {
  // Use SweetAlert2 or any other custom notification logic here
  Swal.fire({
    position: 'top-end',
    icon: 'success',
    title: 'Space',
    text: message,
    showConfirmButton: false,
    timer: 3000 // Adjust the duration as needed
  });
}



function acceptInvite(id, notification_id) 
{
  console.log(notification_id);
  console.log("The value of the groupId is",id);
  let url = '/group/acceptinvite';
  let data = {
    group_id: id
  };
  sendAjaxRequest('POST', url, data, function (response) {
    console.log('Response:', response);
    updateNotification(notification_id);
  });
}


function declineInvite(id,notification_id)
{
  let url = '/group/declineinvite';
  let data = {
    group_id: id
  };
  sendAjaxRequest('DELETE', url, data, function (response) {
    console.log("The value of the status",this.status);
    console.log('Response:', response);
  });
}

function showNotificationC(message) {
  // Use SweetAlert2 or any other custom notification logic here
  Swal.fire({
    position: 'center',
    icon: 'error',
    title: 'Like',
    text: message,
    showConfirmButton: false,
    timer: 3000 // Adjust the duration as needed
  });
}


function acceptJoin(id, group_id) {
  let url = '/group/joinrequest/' + id;
  console.log('the value of the url in accept is', url);
  sendAjaxRequest('POST', url, { id: id, group_id: group_id }, function (response) {
      if (this.status == 200) {
          console.log("The value of the status is", this.status);
          console.log('Response:', response);
          showNotification('You have accepted the join request');

          // Get the join request element with the correct id
          let joinElement = document.querySelector('#join-' + id);

          // Get the username from the join request element
          let username = joinElement.querySelector('p').textContent;

          // Create a new member element
          let memberElement = document.createElement('div');
          memberElement.className = 'member';
          memberElement.id = 'member-' + id;

          // Create the member container element
          let memberContainerElement = document.createElement('div');
          memberContainerElement.className = 'member-container';

          // Create the username element
          let usernameElement = document.createElement('p');
          usernameElement.textContent = username;

          // Append the username element to the member container element
          memberContainerElement.appendChild(usernameElement);

          // Append the member container element to the member element
          memberElement.appendChild(memberContainerElement);

          // Append the member element to the members list
          document.querySelector('.members-card').appendChild(memberElement);

          // Remove the join request element from the HTML
          joinElement.remove();
      }
  });
}

function declineJoin(id, group_id) {
  sendAjaxRequest('DELETE', '/group/joinrequest', { id: id, group_id: group_id }, function (response) {
      if (this.status == 200) {
          showNotificationC('You have declined the join request');
          console.log("the value of the status is ", this.status);
          console.log('Response:', response);

          // Get the join request element with the correct id
          let joinElement = document.querySelector('#join-' + id);

          // Remove the join request element from the HTML
          joinElement.remove();
      }
  });
}

function declineFollowRequest(user_id1,user_id2)
{
  sendAjaxRequest('DELETE', '/profile/followsrequest', {user_id1: user_id1,user_id2:user_id2}, function(response) {
    console.log('Response:', response);
  });
}










function addMessage() {
  console.log('entered in the add message function');
  let url = '/messages/send';
  let data = {
      content: document.getElementById('messageContent').value,
      emits_id: document.getElementById('emitsId').value
  };

  sendAjaxRequest('POST', url, data, function(response) {
      console.log('Response:', response);
  });
}







async function getAPIResult(type, search) {
  // Use a regular expression to allow only letters and numbers
  const sanitizedSearch = search.replace(/[^a-zA-Z0-9]/g, '');

  const query = `/api/${type}?search=${sanitizedSearch}`;
  if(sanitizedSearch == '')
  {
    return '';
  }
  const response = await fetch(query);

  return response.text();
}


function updateTotal(quantity, id) {
let statistic = document.getElementById(id)
if (statistic) {
  statistic.innerHTML = statistic.innerHTML.replace(/\d+/g, quantity)
}
}

function handleUsers() {
  let users = document.getElementById('users');
  let spaces = document.getElementById('spaces');
  let comments = document.getElementById('comments');
  let groups = document.getElementById('groups');

  if(users && users.innerHTML.trim() !== '') {
    users.style.display = 'block';
    spaces.style.display = 'none';
    comments.style.display = 'none';
    groups.style.display = 'none';
  } else {
    users.innerHTML = 'No results found for your search';
  }
}

function handleSpaces() {
  document.getElementById('users').style.display = 'none';
  document.getElementById('spaces').style.display = 'block';
  document.getElementById('comments').style.display = 'none';
  document.getElementById('groups').style.display = 'none';
}

function handleComments() {
  document.getElementById('users').style.display = 'none';
  document.getElementById('spaces').style.display = 'none';
  document.getElementById('comments').style.display = 'block';
  document.getElementById('groups').style.display = 'none';
}

function handleGroups() 
{
  document.getElementById('groups').style.display = 'block';
  document.getElementById('comments').style.display = 'none';
  document.getElementById('spaces').style.display = 'none';
  document.getElementById('users').style.display = 'none';
}

function handlePrincipal() {
  let users = document.getElementById('users');
  let spaces = document.getElementById('spaces');
  let comments = document.getElementById('comments');
  let groups = document.getElementById('groups');

  if(users && users.innerHTML.trim() !== '') users.style.display = 'block';
  else if(users) users.style.display = 'none';

  if(spaces && spaces.innerHTML.trim() !== '') spaces.style.display = 'block';
  else if(spaces) spaces.style.display = 'none';

  if(comments && comments.innerHTML.trim() !== '') comments.style.display = 'block';
  else if(comments) comments.style.display = 'none';

  if(groups && groups.innerHTML.trim() !== '') groups.style.display = 'block';
  else if(groups) groups.style.display = 'none';
}

function toggleFilters() {
  var filters = document.getElementById('filters');
  if (filters.style.display === 'none') {
      filters.style.display = 'block';
  } else {
      filters.style.display = 'none';
  }
}

function handleButtonClick(buttonType) {
  alert(`Button ${buttonType} clicked`);
}


async function search(input) {
  const resultsChats = document.querySelector('#results-chats');
  const resultsUsers = document.querySelector('#results-users');
  const resultsSpaces = document.querySelector('#results-spaces');
  const resultsGroups = document.querySelector('#results-groups');
  const resultsComments = document.querySelector('#results-comments');

  if (resultsChats) {
    resultsChats.innerHTML = await getAPIResult('messages', input);
    updateTotal((resultsChats.innerHTML.match(/<article/g) || []).length, 'messagesResults');
  }

  if (resultsUsers) {
    resultsUsers.innerHTML = await getAPIResult('profile', input);
    updateTotal((resultsUsers.innerHTML.match(/<article/g) || []).length, 'userResults');
  }

  if (resultsSpaces) {
    resultsSpaces.innerHTML = await getAPIResult('space', input);
    updateTotal((resultsSpaces.innerHTML.match(/<article/g) || []).length, 'spaceResults');
  }

  if (resultsGroups) {
    resultsGroups.innerHTML = await getAPIResult('group', input);
    updateTotal((resultsGroups.innerHTML.match(/<article/g) || []).length, 'groupResults');
  }

  if (resultsComments) {
    resultsComments.innerHTML = await getAPIResult('comment', input);
    updateTotal((resultsComments.innerHTML.match(/<article/g) || []).length, 'commentResults');
  }
}

function showResultsContainer() {
  var resultsContainer = document.getElementById('resultsContainer');
  resultsContainer.style.display = 'block';
}

function hideResultsContainer() {
  setTimeout(function() {
      var resultsContainer = document.getElementById('resultsContainer');
      if (document.activeElement !== resultsContainer) {
          resultsContainer.style.display = 'none';
      }
  }, 100);
}


function init() {
const search_bar = document.querySelector("#search");
if (search_bar) {
    let initial_input = window.location.toString().match(/query=(.*)$/g);
    if (initial_input != null) {
        search_bar.value = decodeURIComponent(initial_input[0].replace('query=', ''));
        search(search_bar.value.replace('#', ''));
        search(input);
    }
    search_bar.addEventListener('input', function () {
        let input = this.value.replace('#', '');
        search(input);
    });
}
}
function handleSearchButtonClick() {
  const searchInput = document.querySelector("#search").value;
  
  search(searchInput);
}
init();
