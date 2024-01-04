@include('layouts.app')

@section('content')
    <main class="flex-container">
        @include('partials.sidebar')
        <div class="features-card">
            <h2>Features <i class="fa-solid fa-code"></i></h2>
            <p>Here are some of the features that we offer:</p>
            <table>
                <tr>
                    <th>Role</th>
                    <th>Features</th>
                </tr>
                <tr>
                    <td>Visitor</td>
                    <td>
                        <ul>
                            <li>Login / Logout</li>
                            <li>Register</li>
                            <li>Recover Password</li>
                            <li>View the public feed</li>
                            <li>View public profiles</li>
                            <li>View public spaces</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>User</td>
                    <td>
                        <ul>
                            <li>See a personalized Timeline</li>
                            <li>Search users, comments, groups, and spaces</li>
                            <li>View Profile</li>
                            <li>Edit Profile</li>
                            <li>Delete Profile</li>
                            <li>Edit Profile</li>
                            <li>Upload a profile picture</li>
                            <li>View own Spaces</li>
                            <li>View own Groups</li>
                            <li>View own Notifications</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td>Space</td>
                    <td>
                        <ul>
                            <li>View Space</li>
                            <li>Edit Space</li>
                            <li>Delete Space</li>
                            <li>Comment Space</li>
                            <li>Like Space</li>
                            <li>Upload Image on Space</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td>Group</td>
                    <td>
                        <ul>
                            <li>View Group</li>
                            <li>Edit Group</li>
                            <li>Delete Group</li>
                            <li>Create space inside Group</li>
                            <li>Like Group</li>
                            <li>Invite members to Group</li>
                            <li>Remove members from Group</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td>Message Notification</td>
                    <td>
                        <ul>
                            <li>View Message Notifications</li>
                            <li>Receive Real-Time Message Notifications</li>
                            <li>Mark Message Notifications as Read</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td>Comment</td>
                    <td>
                        <ul>
                            <li>View Comment</li>
                            <li>Edit Comment</li>
                            <li>Delete Comment</li>
                            <li>Like Comment</li>
                            <li>Tag another user in Comment</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td>Notification</td>
                    <td>
                        <ul>
                            <li>View Notification</li>
                            <li>Delete Notification</li>
                            <li>Mark Notification as read</li>
                            <li>Receive notifications in real-time</li>
                            <li>Receive notifications when tagged in a comment</li>
                            <li>Receive notifications when invited to a group</li>
                            <li>Receive notifications when removed from a group</li>
                            <li>Receive notifications when a user likes a comment</li>
                            <li>Receive notifications when a user likes a space</li>
                            <li>Receive notifications when a user adds a group to favorite</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td>Admin</td>
                    <td>
                        <ul>
                            <li>Create accounts </li>
                            <li>Full Text Search </li>
                            <li>Block users </li>
                            <li>Unblock users </li>
                            <li>Delete users </li>
                            <li>Like spaces </li>
                            <li>Delete spaces </li>
                            <li>Edit spaces </li>
                            <li>Delete comment</li>
                            <li>Edit comment</li>
                            <li>Like comment</li>
                            <li>Delete Groups</li>
                            <li>Edit Groups</li>
                            <li>Join Groups</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
        @include('partials.sideSearchbar')
    </main>
    @include('partials.footer')
