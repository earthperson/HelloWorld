#include <iostream>
#include <string>
using namespace std;

int main(int argc, char* argv[]) {
	// 1
	std::cout << "Hello, World (15 November 2011)" << endl;

	// 2
	cout << "Hello, World" << endl;
	char myLine[100];
	cin.getline(myLine, 100);
	cout << myLine << endl;

	// 3
	string myLine2;
	cin >> myLine2;
	cout << myLine2 << endl;

	return 0;
}

