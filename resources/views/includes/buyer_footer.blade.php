<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <img src="{{ asset('assets/images/logo-4.png') }}" height="34" alt="HGH">
                <p class="muted">Fresh from farms. Direct to you.</p>
            </div>

            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ route('buyer.browseProducts') }}">Browse</a></li>
                    <li><a href="{{ route('buyer.history') }}">Orders</a></li>
                </ul>
            </div>

            <div>
                <h4>Support</h4>
                <p>Email: support@homegardenshub.example</p>
            </div>
        </div>

        <div class="copyright">
            &copy; {{ date('Y') }} GreenMarket — All rights reserved.
        </div>
    </div>
</footer>
