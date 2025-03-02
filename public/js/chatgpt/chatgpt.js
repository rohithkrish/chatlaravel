
$(document).ready(function() {
  // Function to send the user message and show it in the chat
  setHistoryTab();
  $('#sendBtn').click(function() {
    var userMessage = $('#userInput').val();
    if (userMessage) {
      // Add user message to the chat box
      $('#chatBox').append('<div class="message user"><p>' + userMessage + '</p></div>');
      $('#userInput').val(''); // Clear the input field

      sendMessage(userMessage);
      // Clear the input field
      $('#userMessage').val('');
      
    }
  });

  // Optional: Enable "Enter" key to send message
  $('#userInput').keypress(function(e) {
    if (e.which == 13) {
      $('#sendBtn').click();
    }
  });

  // Sidebar toggle function
  $('#toggleButton').click(function() {
    $('#sidebar').toggleClass('closed'); // Close/Open the sidebar
    $('#main').toggleClass('shifted');    // Shift main content
  });

});

// Function to send the user message to the server
async function sendMessage(userMessage,id=null) {
  let prompt = userMessage; 
  let csrfToken =$('meta[name="csrf-token"]').attr('content') ;   

  try { 
      
      const response = await fetch("/chat", { 
          method: "POST",
          headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrfToken
          },
          body: JSON.stringify({ prompt ,'id':id})
      });

      if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const reader = response.body.getReader();
      const decoder = new TextDecoder();
      let divisionEnt = $('<div class="message bot"><p></p></div>');
      $('#chatBox').append(divisionEnt);
      while (true) {
          const { value, done } = await reader.read();
          if (done) { setHistoryTab(); break};
          

          // Create and append the initial div
        

          let chunk = decoder.decode(value, { stream: true });
          divisionEnt.find('p').append(chunk);
          // Append the new element to #chatBox
         
          
          if (chunk.includes("[DONE]")){ setHistoryTab();  break};

        
      }
  } catch (error) {
      console.error("Error:", error);
  }

 
}

function setHistoryTab() {
let csrfToken =$('meta[name="csrf-token"]').attr('content') ; 
$.ajax({
  url: '/gethistory',
  type: 'GET',
  headers: {
    "X-CSRF-TOKEN": csrfToken
  },
  success: function(response) {
    if( response.length > 0)
    {
     let elementSideBar = $('#sidebar').find('ul');
     elementSideBar .html('');
      response.forEach(message => {
         let conversationData = message.conversation;
          conversationData.forEach(DataTransfer => {
            if(DataTransfer.role =='user')
                elementSideBar.append('<li id="'+message.id+'">' + DataTransfer.content + '</li>');
          });
        
      });       
    }
    clickEventSideBar();
  }
});
}

function clickEventSideBar() {
$('#sidebar ul li').click(function() {
  let id = $(this).attr('id');
  $('#chatBox').append('<div class="message user"><p>' + $(this).text()+ '</p></div>');
   sendMessage('',id)
}); 

}