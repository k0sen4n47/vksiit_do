@extends('layouts.app')

@section('title-page')
Приветствие
@endsection

@guest
@section('noHeaderFooter', true)
@endguest
@section('content')

<main class="welcome-main">
    <section class="banner">
        <div class="banner__background">
            <div class="banner__shape banner__shape--1"></div>
            <div class="banner__shape banner__shape--2"></div>
            <div class="banner__dots"></div>
            <div class="banner__wave"></div>
        </div>

        <div class="banner__content">
            <h2 class="banner__title title-block">Начните свое обучение сегодня</h2>
            <p class="banner__description">Откройте для себя мир знаний и новых возможностей с нашей платформой дистанционного обучения</p>
            <a href="/login" class="banner__button">Войти</a>
        </div>

        <div class="banner__illustration">
            <div class="banner__illustration-item banner__illustration-item--book">
                <img src="{{ asset('images/1.png') }} ">
            </div>
            <div class=" banner__illustration-item banner__illustration-item--laptop">
                <img src="{{ asset('images/2.jpg') }}">
            </div>
            <div class="banner__illustration-item banner__illustration-item--certificate">
                <img src="{{ asset('images/3.jpg') }}">
            </div>
        </div>
    </section>

    <section class="advantages">
        <div class="advantages__container">
            <header class="advantages__header" data-aos="fade-up">
                <h2 class="advantages__title title-block">Преимущества дистанционного обучения</h2>
                <p class="advantages__subtitle">
                    Современный подход к образованию, который открывает новые возможности для каждого студента
                </p>
            </header>

            <div class="advantages__grid">
                <div class="advantages__card advantage-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="advantage-card__icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="advantage-card__title">Гибкое расписание</h3>
                    <p class="advantage-card__description">
                        Учитесь в удобное время, совмещая обучение с работой и личными делами
                    </p>
                </div>

                <div class="advantages__card advantage-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="advantage-card__icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3 class="advantage-card__title">Доступность</h3>
                    <p class="advantage-card__description">
                        Получайте образование из любой точки мира, где есть интернет
                    </p>
                </div>

                <div class="advantages__card advantage-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="advantage-card__icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="advantage-card__title">Индивидуальный темп</h3>
                    <p class="advantage-card__description">
                        Изучайте материал в своем ритме, возвращайтесь к сложным темам
                    </p>
                </div>

                <div class="advantages__card advantage-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="advantage-card__icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <h3 class="advantage-card__title">Экономия средств</h3>
                    <p class="advantage-card__description">
                        Сэкономьте на проезде, проживании и учебных материалах
                    </p>
                </div>

                <div class="advantages__card advantage-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="advantage-card__icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3 class="advantage-card__title">Современные технологии</h3>
                    <p class="advantage-card__description">
                        Интерактивные уроки, вебинары и мультимедийные материалы
                    </p>
                </div>

                <div class="advantages__card advantage-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="advantage-card__icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="advantage-card__title">Персональный подход</h3>
                    <p class="advantage-card__description">
                        Индивидуальные консультации с преподавателями и кураторами
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Новый блок: Этапы обучения -->
    <section class="steps fade-in">
        <div class="steps__container">
            <header class="steps__header fade-in-up fade-delay-1" data-aos="fade-up">
                <h2 class="steps__title title-block">Этапы дистанционного обучения</h2>
                <p class="steps__subtitle">Как проходит процесс обучения на нашей платформе</p>
            </header>
            <div class="steps__list">
                <div class="steps__item fade-in-up fade-delay-2" data-aos="fade-up" data-aos-delay="100">
                    <span class="steps__number">1</span>
                    <h3 class="steps__item-title">Регистрация и выбор курса</h3>
                    <p class="steps__item-desc">Создайте аккаунт, выберите интересующий предмет и группу.</p>
                </div>
                <div class="steps__item fade-in-up fade-delay-3" data-aos="fade-up" data-aos-delay="200">
                    <span class="steps__number">2</span>
                    <h3 class="steps__item-title">Изучение материалов</h3>
                    <p class="steps__item-desc">Получайте доступ к лекциям, тестам и практическим заданиям в удобное время.</p>
                </div>
                <div class="steps__item fade-in-up fade-delay-4" data-aos="fade-up" data-aos-delay="300">
                    <span class="steps__number">3</span>
                    <h3 class="steps__item-title">Обратная связь</h3>
                    <p class="steps__item-desc">Задавайте вопросы преподавателям, участвуйте в обсуждениях и получайте поддержку.</p>
                </div>
                <div class="steps__item fade-in-up fade-delay-5" data-aos="fade-up" data-aos-delay="400">
                    <span class="steps__number">4</span>
                    <h3 class="steps__item-title">Контроль знаний</h3>
                    <p class="steps__item-desc">Выполняйте тесты и задания, отслеживайте свой прогресс в личном кабинете.</p>
                </div>
                <div class="steps__item fade-in-up fade-delay-6" data-aos="fade-up" data-aos-delay="500">
                    <span class="steps__number">5</span>
                    <h3 class="steps__item-title">Получение сертификата</h3>
                    <p class="steps__item-desc">По завершении курса получите электронный сертификат о прохождении обучения.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Новый блок: Вопрос-Ответ -->
    <section class="faq fade-in">
        <div class="faq__container">
            <header class="faq__header fade-in-up fade-delay-1" data-aos="fade-up">
                <h2 class="faq__title title-block">Часто задаваемые вопросы</h2>
            </header>
            <div class="faq__list">
                <div class="faq__item fade-in-up fade-delay-2" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="faq__question">Как записаться на курс?</h3>
                    <p class="faq__answer">Пройдите регистрацию, выберите нужный курс и группу, после чего получите доступ к материалам.</p>
                </div>
                <div class="faq__item fade-in-up fade-delay-3" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="faq__question">Можно ли учиться с мобильного устройства?</h3>
                    <p class="faq__answer">Да, платформа адаптирована для работы на смартфонах и планшетах.</p>
                </div>
                <div class="faq__item fade-in-up fade-delay-4" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="faq__question">Как получить помощь по учебным вопросам?</h3>
                    <p class="faq__answer">Вы можете задать вопрос преподавателю через личный кабинет или воспользоваться форумом поддержки.</p>
                </div>
                <div class="faq__item fade-in-up fade-delay-5" data-aos="fade-up" data-aos-delay="400">
                    <h3 class="faq__question">Что делать, если не получается войти?</h3>
                    <p class="faq__answer">Воспользуйтесь функцией восстановления пароля или обратитесь в службу поддержки.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Новый блок: Контакты -->
    <section class="contacts fade-in">
        <div class="contacts__container">
            <header class="contacts__header fade-in-up fade-delay-1" data-aos="fade-up">
                <h2 class="contacts__title">Контакты</h2>
                <p class="contacts__subtitle">Свяжитесь с нами по любым вопросам</p>
            </header>
            <div class="contacts__info">
                <div class="contacts__item fade-in-up fade-delay-2">
                    <i class="fas fa-envelope"></i>
                    <span class="contacts__label">Email:</span>
                    <a href="mailto:zaharka25t@gmail.com" class="contacts__link">zaharka25t@gmail.com</a>
                </div>
                <div class="contacts__item fade-in-up fade-delay-3">
                    <i class="fas fa-phone"></i>
                    <span class="contacts__label">Телефон:</span>
                    <a href="tel:+79115033173" class="contacts__link">8 911 503-31-73</a>
                </div>
                <div class="contacts__item fade-in-up fade-delay-4">
                    <i class="fas fa-paper-plane"></i>
                    <span class="contacts__label">Telegram:</span>
                    <a href="https://t.me/+P4e9xx1ilJZjNWZi" class="contacts__link" target="_blank">Колледж связи от Тюлькина</a>
                </div>
            </div>
            <div class="contacts__actions fade-in-up fade-delay-5">
                <a href="mailto:support@edu-platform.ru" class="banner__button contacts__button">Написать нам</a>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const animatedEls = document.querySelectorAll('.fade-in, .fade-in-up');
    animatedEls.forEach(el => {
        el.classList.add('fade-init'); // временный класс для невидимости
    });
    const observer = new window.IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('fade-init');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    animatedEls.forEach(el => {
        observer.observe(el);
    });
});
</script>
<style>
.fade-init {
    opacity: 0 !important;
    pointer-events: none;
    transition: none !important;
}
</style>
@endpush