document.querySelector('#userSearch').addEventListener('input', (e) => search(e.target.value, 'users'));
document.querySelector('#spacesSearch').addEventListener('input', (e) => search(e.target.value, 'spaces'));
document.querySelector('#groupsSearch').addEventListener('input', (e) => search(e.target.value, 'groups'));

function GroupsDropDown() {
    var searchUsers = document.getElementById('adminUsersSearch');
    var searchGroups = document.getElementById('adminGroupsSearch');
    var searchSpaces = document.getElementById('adminSpacesSearch');
    var createUser = document.getElementById('createUser');
    if (searchGroups.style.display === 'none') {
        searchGroups.style.display = 'block';
        searchUsers.style.display = 'none';
        searchSpaces.style.display = 'none';
        createUser.style.display = 'none';
    } else {
        searchGroups.style.display = 'none';
    }
}

function SpacesDropDown() {
    var searchUsers = document.getElementById('adminUsersSearch');
    var searchGroups = document.getElementById('adminGroupsSearch');
    var searchSpaces = document.getElementById('adminSpacesSearch');
    var createUser = document.getElementById('createUser');
    if (searchSpaces.style.display === 'none') {
        searchSpaces.style.display = 'block';
        searchUsers.style.display = 'none';
        searchGroups.style.display = 'none';
        createUser.style.display = 'none';
    } else {
        searchSpaces.style.display = 'none';
    }
}

function UsersDropDown() {
    var searchUsers = document.getElementById('adminUsersSearch');
    var searchGroups = document.getElementById('adminGroupsSearch');
    var searchSpaces = document.getElementById('adminSpacesSearch');
    var createUser = document.getElementById('createUser');
    if (searchUsers.style.display === 'none') {
        searchUsers.style.display = 'block';
        createUser.style.display = 'block';
        searchGroups.style.display = 'none';
        searchSpaces.style.display = 'none';
    } else {
        searchUsers.style.display = 'none';
        createUser.style.display = 'none';
    }
}