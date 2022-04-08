<!doctype html>
<html>
<head>
    <title>CodeIgniter Tutorial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@walletconnect/web3-provider@1.7.1/dist/umd/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
    <script type="text/javascript">
        var account;

        // https://docs.walletconnect.com/quick-start/dapps/web3-provider
        var provider = new WalletConnectProvider.default({
            rpc: {
                1: "https://cloudflare-eth.com/", // https://ethereumnodes.com/
                137: "https://polygon-rpc.com/", // https://docs.polygon.technology/docs/develop/network-details/network/
                // ...

            },
            // bridge: 'https://bridge.walletconnect.org',
        });


        var connectWC = async () => {
            await provider.enable();

            //  Create Web3 instance
            const web3 = new Web3(provider);
            console.log(web3)
            window.w3 = web3
            console.log(w3)

            var accounts  = await web3.eth.getAccounts(); // get all connected accounts
            account = accounts[0]; // get the primary account
        }


        var payw3 = async () => {
            if (w3) {
                console.log('w3 found')
                const toAddress = "0xc018eE41e8BcC6ef7dB6d0b86D738a2Aa8b438DA";
                await w3.eth.sendTransaction({ from: account, to: toAddress, value: '100000000'})

            } else {
                return false
            }
        }

        var disconnect = async () => {
        // Close provider session
        await provider.disconnect()
        }

    </script>
        
</head>
<body>

    <h1><?= esc($title) ?></h1>

    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <button onclick="connectWC()">Connect Wallet Connect</button>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <button onclick="payw3()">Pay with WalletConnect</button>
            </div>
        </div>
    </div>


