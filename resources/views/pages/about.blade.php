@extends('layouts.app')

@section('content')
<main class="flex-container">
    @include('partials.sidebar')
    <div class="about-card">
        <h2>Welcome to SportHUB!</h2>

        <p>SportHUB is more than just a platform; it's a thriving community that brings together sports enthusiasts from
            all walks of life. Our mission is to create a space where athletes and fans can connect, share their
            passion, and engage in meaningful discussions about the sports they love.</p>

        <h3>Our Goals</h3>

        <p>The primary goals of the SportHUB project are centered around fostering interaction and building connections
            within the sports community:</p>

        <ul>
            <li><strong>Share and Discuss:</strong> SportHUB enables users to share their thoughts, experiences, and
                insights on a wide range of sports-related topics. Whether it's the latest game, a team's performance,
                or a sports event, users can post and engage in discussions.</li>

            <li><strong>Connect with Friends:</strong> We believe in the power of camaraderie. SportHUB allows users to
                connect with friends and fellow sports enthusiasts. Through friend requests, you can expand your network
                and stay updated on the latest activities and discussions within your circle.</li>

            <li><strong>Form Groups:</strong> Simplifying the process of forming groups is another key feature. Whether
                you want to gather teammates for a casual chat or have more structured discussions about your shared
                passion for sports, SportHUB provides the tools to make it happen.</li>
        </ul>

        <h3>Join the Community</h3>

        <p>Ready to dive into the world of sports conversations and connections? Join SportHUB today and become a part
            of a vibrant community that celebrates the spirit of sports. Whether you're an athlete, a dedicated fan, or
            someone looking to explore the sports landscape, SportHUB is the place for you.</p>

        <p>Let's build a community where the love for sports unites us all!</p>

        <h3>Contacts</h3>
        <p>For any questions or concerns, please contact us at : <p>
            <ul>
                <li>up202105337@fe.up.pt</li>
                <li>up202108711@fe.up.pt</li>
                <li>up202108661@fe.up.pt</li>
            </ul>
        <div class="icon-center">
            <i class="fa-solid fa-futbol white-icon"></i> <i class="fa-solid fa-basketball white-icon"></i> <i
                class="fa-solid fa-volleyball white-icon"></i>
        </div>
    </div>
    @include('partials.sideSearchbar')
</main>
@include('partials.footer')
@endsection