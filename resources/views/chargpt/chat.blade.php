@extends('welcome')
@section('title', 'chat gpt')
@section('css')
<link href="{{ asset('css/chatgpt/chatgpt.css') }}" rel="stylesheet">
@endsection
@section('content')

    <!-- Sidebar -->
    <div id="sidebar">
        <h2>ChatGPT</h2>
        <ul>
          <li>Home</li>
          <li>About</li>
          <li>Settings</li>
          <li>Logout</li>
        </ul>
      </div>
    
      <!-- Toggle Button for Sidebar -->
      <button id="toggleButton">☰</button>
    
      <!-- Main Content -->
      <div id="main">
        <!-- Chat Box -->
        <div id="chatBox">
          <div class="message bot">
            <p>Hi! How can I assist you today?</p>
          </div>
        </div>
    
        <!-- Input Box -->
        <div id="inputBox">
          <input type="text" id="userInput" placeholder="Type a message..." />
          <button id="sendBtn">Send</button>
        </div>
      </div>

@endsection

@section('js')
<script src="{{ asset('js/chatgpt/chatgpt.js') }}"></script>
@endsection