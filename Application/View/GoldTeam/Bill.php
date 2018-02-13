<section class="invoice bg-gray">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-globe"></i> Miles Systems LLC
                <small class="pull-right"><?= date('m/d/Y', filemtime(__FILE__)) ?></small>
            </h2>
        </div>
        <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-12 invoice-col">
            <h3 style="margin-top: 0">Product • Pricing Example</h3>
        </div>
        <br><br>
        <div class="col-sm-4 invoice-col">
            From
            <address>
                <strong>Miles Systems LLC</strong><br>
                4906 Wimberly ln<br>
                Bayton, TX 76201<br>
                Phone: (817) 789-3294<br>
                Email: Richard@Miles.Systems
            </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
            To
            <address>
                <strong>John Doe</strong><br>
                795 Folsom Ave, Suite 600<br>
                San Francisco, CA 94107<br>
                Phone: (555) 539-1037<br>
                Email: john.doe@example.com
            </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
            <b>Invoice #007612</b><br>
            <br>
            <b>Order ID:</b> 4F3S8J<br>
            <b>Payment Due:</b> <?= $date = date('m/d/Y', strtotime('+2 week',(new DateTime)->getTimestamp())) ?><br>
            <b>Account:</b> 968-34567
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Qty</th>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Custom Graphic Solutions</td>
                    <td>Logo Creation, Business Cards, Background Graphics</td>
                    <td>$85.00</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>.COM Domain Name Registration</td>
                    <td>1 Year Domain Registration (Reoccurring)</td>
                    <td>$14.00</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Progressive Web Application</td>
                    <td>example.com</td>
                    <td>$300.00</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Scalable Server Hosting</td>
                    <td>1 year of web hosting (Reoccurring)</td>
                    <td>$70.00</td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
            <p class="lead">Payment Methods:</p>
            <img src="<?=SITE.TEMPLATE?>dist/img/credit/visa.png" alt="Visa">
            <img src="<?=SITE.TEMPLATE?>dist/img/credit/mastercard.png" alt="Mastercard">
            <img src="<?=SITE.TEMPLATE?>dist/img/credit/american-express.png" alt="American Express">
            <img src="<?=SITE.TEMPLATE?>dist/img/credit/paypal2.png" alt="Paypal">

            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                This page is an example invoice, and price points may very based on time spent, project complexity,
                storage, and electricity needs. At Miles Systems LLC we have solutions for every technical aspect. If
                you choose to host with us, our self scaling servers will ensure you are only using the power you need.
                This saves you money and our environment, so lets innovate together.
            </p>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
            <p class="lead">Amount Due <?=$date?></p>

            <div class="table-responsive">
                <table class="table">
                    <tbody><tr>
                        <th style="width:50%">Subtotal:</th>
                        <td>$250.30</td>
                    </tr>
                    <tr>
                        <th>Tax (9.3%)</th>
                        <td>$10.34</td>
                    </tr>
                    <tr>
                        <th>Shipping:</th>
                        <td>$5.80</td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td>$265.24</td>
                    </tr>
                    </tbody></table>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- this row will not appear when printing -->
    <div class="row no-print">
        <div class="col-xs-12">
            <a onclick="window.print()" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
            <button type="button" class="btn btn-danger pull-right"><i class="fa fa-credit-card"></i> Submit Payment
            </button>
            <button onclick="window.print()" type="button" class="btn bg-black pull-right" style="margin-right: 5px;">
                <i class="fa fa-download"></i> Generate PDF
            </button>
        </div>
    </div>
</section>