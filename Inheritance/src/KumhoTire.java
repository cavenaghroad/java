
public class KumhoTire extends Tire {
	public KumhoTire(int maxRotation, String location) {
		super(maxRotation, location);
	}
	
	@Override
	public boolean roll() {
		++pastRotation;
		if(pastRotation<maxRotation) {  
			System.out.println("금호타이어 남은수명: "+(maxRotation-pastRotation)+"회");
			return true;   // 최대회전수보다 적게 주행
		} else {
			System.out.println(this.location+" 금호타이어펑크");
			return false;	// 최대회전수 초과
		}
	}
}
