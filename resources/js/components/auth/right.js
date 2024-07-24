import { Image,} from 'antd';

export default function Right() {
    
    return (
        <Image
            preview={false}
            width="100%"
            height="100vh"
            src="../img/login-image2x.png"
            style={{objectFit: "cover"}}
        />
    );
}