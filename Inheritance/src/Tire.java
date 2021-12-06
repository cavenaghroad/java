
public class Tire {
	int maxRotation;   // 최대회전횟수
	int pastRotation;  // 누적회전횟수
	String location;	// 타이어위치
	
	public Tire() {}

	public Tire(int maxRotation, String location) {
		this.maxRotation = maxRotation;
		this.location = location;
	}
	
	public boolean roll() {
		++pastRotation;
		if(pastRotation<maxRotation) {  
			System.out.println("Tire 남은수명: "+(maxRotation-pastRotation)+"회");
			return true;   // 최대회전수보다 적게 주행
		} else {
			System.out.println(this.location+" 타이어펑크");
			return false;	// 최대회전수 초과
		}
	}

}
