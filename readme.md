LitecoinPledge
==============

LitecoinPledge is a site to encourage donations using Litecoin. It offers
monthly, one-time, and anonymous donations.

License
=======

**LitecoinPledge** is licensed under the GNU Affero GPL. This means (in simple
terms) that you CANNOT use LitecoinPledge's source code without releasing your
modifications to the public, as well as stating that it is a modification of
LitecoinPledge.

If you would like to use LitecoinPledge outside of this license, please contact
the creator, **Someguy123** prior to using the source code.

Install
=======
Clone the repository

    git clone https://github.com/someguy123/litecoinpledge.git
    cd litecoinpledge

Set permissions correctly

    chown -R www-data:www-data storage
    chmod -R 777 storage

Configure your `.env` file based on .env.example

NOTE: With the default of `APP_DEBUG=true`, the application will use the
`Someguy123\BitcoinConnector\MockBitcoin` class for the Litecoin daemon, you
will need to disable debug to use the Litecoin daemon defined in `.env`

Install dependencies

    composer install
    npm install

Run gulp (compile SCSS)

    #if you don't already have gulp, install it globally
    npm install -g gulp
    #finally run gulp
    gulp

Migrate the database

    php artisan migrate

Finally - set up crons. Recommended to use `lckdo` from `moreutils` for safety.
Adjust times as necessary.

    0       */12    *       *       *        lckdo /tmp/pledge_schedule.lck php /INSTALL_DIR/artisan lp:pledge_schedule
    */10    *       *       *       *        lckdo /tmp/fillpool.lck php /INSTALL_DIR/artisan ltc:fillpool
    */3     *       *       *       *        lckdo /tmp/transactions.lck php /INSTALL_DIR/artisan ltc:transactions

Donations
=========
Donate to LitecoinPledge via [LitecoinPledge](https://www.litecoinpledge.org/projects/1)

You can do this anonymously (LTC Address), as well as using account funds to pledge monthly, or just one-time.
