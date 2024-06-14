<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
          // $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          // $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
          // if(mysqli_num_rows($sql) > 0){
          //   $row = mysqli_fetch_assoc($sql);
          // }else{
          //   header("location: users.php");
          // }
        ?>
    
        <a href="/users" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="/images/<?php echo $user[0]->img;//echo $row['img']; ?>" alt="">
        <div class="details">
          <span><?php echo $user[0]->fname. " " . $user[0]->lname ?></span>
          <p><?php echo $user[0]->status; ?></p>
        </div>
      </header>
      <div class="chat-box">

      </div>
      <form action="#" class="typing-area">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user[0]->unique_id; ?>" hidden>
        <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <script src="<?= base_url()?>js/chat.js"></script>

</body>
</html>
