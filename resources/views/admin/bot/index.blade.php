
<!-- Rankolab Bot UI -->
<div class="card shadow">
  <div class="card-header bg-dark text-white">Rankolab Bot (Super Admin Only)</div>
  <div class="card-body">
    <div id="chat-box" style="height:300px; overflow-y:auto; background:#f9f9f9;" class="mb-3 p-2 border rounded"></div>
    <form id="bot-form">
        <input type="text" class="form-control mb-2" name="message" placeholder="Ask me anything..." required />
        <button class="btn btn-primary w-100" type="submit">Send</button>
    </form>
  </div>
</div>

<script>
document.getElementById('bot-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = this.message.value;
    const chatBox = document.getElementById('chat-box');
    chatBox.innerHTML += '<div><strong>You:</strong> ' + input + '</div>';

    fetch('/admin/bot/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ message: input })
    })
    .then(res => res.json())
    .then(data => {
        chatBox.innerHTML += '<div><strong>Bot:</strong> ' + JSON.stringify(data.reply) + '</div>';
        chatBox.scrollTop = chatBox.scrollHeight;
    });
});
</script>
