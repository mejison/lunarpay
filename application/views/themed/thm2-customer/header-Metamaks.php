<!doctype html>
<html>
<head>
    <title>CodeIgniter Tutorial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.ethers.io/lib/ethers-5.2.umd.min.js"></script>
    <script>
        async function web3Login() {
            console.log('login');
            if (!window.ethereum) {
                alert('MetaMask not detected. Please install MetaMask first.');
                return;
            }
            console.log('metamask found');
            const accounts = await ethereum.request({ method: "eth_requestAccounts" });
            console.log("Connected", accounts[0]);
            setCurrentAccount(accounts[0]); 
        }
    </script>
    <script>
        var amount = '0.001'
        async function web3Pay() {
            const toAddress = "0xc018eE41e8BcC6ef7dB6d0b86D738a2Aa8b438DA";
            console.log('toAddress');
                const { ethereum } = window;
            
                if (ethereum) {
                    const provider = new ethers.providers.Web3Provider(ethereum);
                    const signer = provider.getSigner();
                    // Ether amount to send

                    //var amountInEther = '0.001'


                    // Create a transaction object
                    let tx = {
                        to: toAddress,
                        // Convert currency unit from ether to wei
                        value: ethers.utils.parseEther(amount)
                    }
                    // Send a transaction
                    signer.sendTransaction(tx)
                    .then((txObj) => {
                        console.log('txHash', txObj.hash)
                    })
                }
              
        }
        function docWrite(variable) {
            document.write(variable);
        }          
    </script>
        
</head>
<body>

    <h1><?= esc($title) ?></h1>



    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <button class="btn btn-primary mt-5" onclick="web3Login();">Log in with MetaMask</button>
            </div>
        </div>
    </div>
    <h1>

    </h1>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
            Pay amount: <script>docWrite(amount)</script>
                <button class="btn btn-primary mt-5" onclick="web3Pay();">Pay</button>
            </div>
        </div>
    </div>

