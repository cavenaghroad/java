
public class HankookTire extends Tire {

	public HankookTire(int maxRotation, String location) {
		super(maxRotation, location);
	}
	
	@Override
	public boolean roll() {
		++pastRotation;
		if(pastRotation<maxRotation) {  
			System.out.println("한국타이어 남은수명: "+(maxRotation-pastRotation)+"회");
			return true;   // 최대회전수보다 적게 주행
		} else {
			System.out.println(this.location+" 한국타이어펑크");
			return false;	// 최대회전수 초과
		}
	}
}
