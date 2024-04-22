<div class="col-12">
    <div class="row my-5 ">
        <div class="col-md-5">
            <div class="card card-outline card-navy rounded-0 shadow">
                <div class="card-header">
                    <h4 class="card-title">Contact</h4>
                </div>
                <div class="card-body rounded-0">
                    <dl>
                        <dt class="text-muted"><i class="fa fa-envelope"></i> Email</dt>
                        <dd class="pl-4"><?= $_settings->info('email') ?></dd>
                        <dt class="text-muted"><i class="fa fa-phone"></i> Contact #</dt>
                        <dd class="pl-4"><?= $_settings->info('contact') ?></dd>
                        <dt class="text-muted"><i class="fa fa-map-marked-alt"></i> Location</dt>
                        <dd class="pl-4"><?= $_settings->info('address') ?></dd>
                        <dt class="text-muted"><i class="fa fa-clock"></i> Daily Schedule</dt>
                        <dd class="pl-4"><?= $_settings->info('clinic_schedule') ?></dd>
                        <dt class="text-muted"><i class="fa fa-paw"></i> Maximum Daily Appointments</dt>
                        <dd class="pl-4"><?= $_settings->info('max_appointment') ?></dd>
                        <p><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d30850.83583539407!2d120.241943!3d14.861444000000002!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x339677b9a924c997%3A0xe75aa9258d0bb42b!2sAnimal&#39;s%20Choice%20Veterinary%20Clinic!5e0!3m2!1sen!2sph!4v1712285712731!5m2!1sen!2sph" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></p>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card rounded-0 card-outline card-navy shadow" >
                <div class="card-body rounded-0">
                    <h2 class="text-center">About</h2>
                    <center><hr class="bg-navy border-navy w-25 border-2"></center>
                    <div>
                        <?= file_get_contents("about_us.html") ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>