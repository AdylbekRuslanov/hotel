<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'Нет свободных номеров';
   }else{
      $success_msg[] = 'Номера свободные';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'комнта не свободна';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'комната уже забронирована';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'Бронь прошла успешна, мы свяжимся свами в течении часа';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Главная</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/b1.jpg" alt="">
            <div class="flex">
               <h3>Роскошные номера</h3>
               <a href="#availability" class="btn">проверить наличие свободных номеров</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-2.jpg" alt="">
            <div class="flex">
               <h3>еда и напитки</h3>
               <a href="#reservation" class="btn">зарезервировать</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/home-img-3.jpg" alt="">
            <div class="flex">
               <h3>роскошные залы</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>дата приезда <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>дата отьезда <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>взрослый <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 взрослый</option>
               <option value="2">2 взрослый</option>
               <option value="3">3 взрослый</option>
               <option value="4">4 взрослый</option>
               <option value="5">5 взрослый</option>
               <option value="6">6 взрослый</option>
            </select>
         </div>
         <div class="box">
            <p>дети <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 детей</option>
               <option value="1">1 детский</option>
               <option value="2">2 детский</option>
               <option value="3">3 детский</option>
               <option value="4">4 детский</option>
               <option value="5">5 детский</option>
               <option value="6">6 детский</option>
            </select>
         </div>
         <div class="box">
            <p>комнаты <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">1 комната</option>
               <option value="2">2 комнаты</option>
               <option value="3">3 комнаты</option>
               <option value="4">4 комнаты</option>
               <option value="5">5 комнаты</option>
               <option value="6">6 комнаты</option>
            </select>
         </div>
      </div>
      <input type="submit" value="проверить наличия свободных номеров" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/about-img-1.jpg" alt="">
      </div>
      <div class="content">
         <h3>Персонал</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi laborum maxime eius aliquid temporibus unde?</p>
         <a href="#reservation" class="btn">зарезервировать</a>
      </div>
   </div>

   <div class="row revers">
      <div class="image">
         <img src="images/about-img-2.jpg" alt="">
      </div>
      <div class="content">
         <h3>Вкусные блюда</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi laborum maxime eius aliquid temporibus unde?</p>
         <a href="#contact" class="btn">связаться с нами</a>
      </div>
   </div>

   <div class="row">
      <div class="image">
         <img src="images/b2.jpg" alt="">
      </div>
      <div class="content">
         <h3>Бассейн</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi laborum maxime eius aliquid temporibus unde?</p>
         <!-- <a href="#availability" class="btn">check availability</a> -->
      </div>
   </div>

</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>Еда и напитки</h3>
         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, sunt?</p>
      </div>

      <!-- <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>outdoor dining</h3>
         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, sunt?</p>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>beach view</h3>
         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, sunt?</p>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>decorations</h3>
         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, sunt?</p>
      </div> -->

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>бассейн</h3>
         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, sunt?</p>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>тренажерный зал</h3>
         <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, sunt?</p>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>забронировать</h3>
      <div class="flex">
         <div class="box">
            <p>ваше имя <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="ваше имя" class="input">
         </div>
         <div class="box">
            <p>ваш email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="ваш email" class="input">
         </div>
         <div class="box">
            <p>ваш номер <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="ваш номер" class="input">
         </div>
         <div class="box">
            <p>комнаты <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1" selected>1 комната</option>
               <option value="2">2 комнаты</option>
               <option value="3">3 комнаты</option>
               <option value="4">4 комнаты</option>
               <option value="5">5 комнаты</option>
               <option value="6">6 комнаты</option>
            </select>
         </div>
         <div class="box">
            <p>дата приезда <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>дата отьезда <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>взрослый <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 взрослый</option>
               <option value="2">2 взрослых</option>
               <option value="3">3 взрослых</option>
               <option value="4">4 взрослых</option>
               <option value="5">5 взрослых</option>
               <option value="6">6 взрослых</option>
            </select>
         </div>
         <div class="box">
            <p>дети <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 детей</option>
               <option value="1">1 десткий</option>
               <option value="2">2 детских</option>
               <option value="3">3 детских</option>
               <option value="4">4 детских</option>
               <option value="5">5 детских</option>
               <option value="6">6 детских</option>
            </select>
         </div>
      </div>
      <input type="submit" value="Забронировать" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->
<!-- class="swiper gallery-slider" -->
<!-- <section class="about" id="about">
<div class="row">
   
      <div class="content">
      <h3>Люксовые номера</h3>
      </div>
   </div>
</section>


<section class="gallery" id="gallery">
   <div  class="swiper gallery-slider">
      <div  class="swiper-wrapper">
         <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section> -->

<section class="about" id="about">
<div class="row">
   
      <div class="content">
      <h3>Номера</h3>
      </div>
   </div>
</section>

<section class="gallery" id="gallery">

   <div  class="swiper gallery-slider">
      <div  class="swiper-wrapper">
         <img src="images/b3.jpg" class="swiper-slide" alt="">
         <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
         <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
         <img src="images/b4.jpg" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>Отправить отзыв</h3>
         <input type="text" name="name" required maxlength="50" placeholder="введите имя" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="введите email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="введите номер" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="ваше сообщение" cols="30" rows="10"></textarea>
         <input type="submit" value="Отправить" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">Часто задаваемые вопросы</h3>
         <div class="box active">
            <h3>Как отменить бронь?</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Natus sunt aspernatur excepturi eos! Quibusdam, sapiente.</p>
         </div>
         <div class="box">
            <h3>Есть ли скидка?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div>
         <div class="box">
            <h3>Какие способы оплаты?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div>
         <div class="box">
            <h3>Как забронировать?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div>
         <!-- <div class="box">
            <h3>what are the age requirements?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa ipsam neque quaerat mollitia ratione? Soluta!</p>
         </div> -->
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<!-- <section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/pic-1.png" alt="">
            <h3>Таалайбек Сагыналиев</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-2.png" alt="">
            <h3>john deo</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-3.png" alt="">
            <h3>john deo</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-4.png" alt="">
            <h3>john deo</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-5.png" alt="">
            <h3>john deo</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/pic-6.png" alt="">
            <h3>john deo</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates blanditiis optio dignissimos eaque aliquid explicabo.</p>
         </div>
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section> -->

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>